<?php
require_once('authenticate.php');
$database   = 'gpsDB';
$collection = 'history';
 
/**
 * MongoDB connection
 */
try {
    $m = new MongoClient();
} catch (MongoConnectionException $e) {
    die('Error connecting to MongoDB server');
}
 
$m_collection = $m->$database->$collection;

 /**
 * Define the document fields to return to DataTables (as in http://us.php.net/manual/en/mongocollection.find.php).
 * If empty, the whole document will be returned.
 */
$fields = array();

// Input method (use $_GET, $_POST or $_REQUEST)
$input =& $_POST;
 
 
$columns = $input['columns'];
$columnsSize = sizeof($columns);
 
// Searching
$searchTermsAny = array();
$searchTermsAll = array();
$search = $input['search'];
if ( !empty($search['value']) ) {
    $searchText = $search['value'];
    for ( $i=0 ; $i < $columnsSize ; $i++ ) {
        if ($columns[$i]['searchable'] == 'true') {
            if ($search['regex'] == 'true') {
                $sRegex = str_replace('/', '\/', $searchText);
            } else {
                $sRegex = preg_quote($searchText, '/');
            }
            $searchTermsAny[] = array(
                $columns[$i]['data'] => new MongoRegex( '/'.$sRegex.'/i' )
            );
        }
    }
}
 
// Individual column filtering
for ( $i=0 ; $i < $columnsSize ; $i++ ) {
    if ( $columns[$i]['searchable'] == 'true' && $columns[$i]['search']['value'] != '' ) {
        if ($columns[$i]['search']['regex'] == 'true') {
            $sRegex = str_replace('/', '\/', $columns[$i]['search']['value']);
        } else {
            $sRegex = preg_quote($columns[$i]['search']['value'], '/');
        }
        $searchTermsAll[ $columns[$i]['data'] ] = new MongoRegex( '/'.$sRegex.'/i' );
    }
}

//date filtering
$minDate = $input['minDate'];
$maxDate = $input['maxDate'];
$filter = array();
if(!empty($minDate))
{
	$filter['$gte'] = $minDate;
}
if(!empty($maxDate))
{
	$filter['$lte'] = $maxDate;
}
if(sizeof($filter))
{
	$searchTermsAll['Date'] = $filter;
}

$searchTerms = $searchTermsAll;
if (!empty($searchTermsAny)) {
    $searchTerms['$or'] = $searchTermsAny;
}


$cursor = $m_collection->find($searchTerms, $fields);

// Paging
 
if ( isset( $input['start'] ) && $input['length'] != '-1' ) {
    $cursor->limit( intval($input['length']) )->skip(intval($input['start']));
}

$order = $input['order'];
$orderSize = sizeof($order);

// Ordering
 if ( isset($order) ) {
    $sort_fields = array();
    for ( $i=0 ; $i<intval( $orderSize ) ; $i++ ) {
		$orderCol = $order[$i]['column'];
		$orderDir = $order[$i]['dir'];
        if ( $columns[$orderCol]['orderable'] == 'true' ) {
            $field = $columns[$orderCol]['data'];
            $order = ( $orderDir == 'desc' ? -1 : 1 );
            $sort_fields[$field] = $order;
        }
    }
    $cursor->sort($sort_fields);
}

// Output

$output = array(
    "draw" => intval($input['draw']),
    "iTotalRecords" => $m_collection->count(),
    "iTotalDisplayRecords" => $cursor->count(),
    "data" => array(),
);

foreach ( $cursor as $doc ) {
    $output['data'][] = $doc;
}
 
echo json_encode( $output );