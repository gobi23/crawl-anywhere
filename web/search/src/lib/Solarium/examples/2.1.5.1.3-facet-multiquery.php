<?php

require('init.php');
htmlHeader();

// create a client instance
$client = new Solarium_Client($config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet query instance and set options
$facet = $facetSet->createFacetMultiQuery();
$facet->setKey('stock');
$facet->createQuery('stock_pricecat1', 'inStock:true AND price:[1 TO 300]');
$facet->createQuery('nostock_pricecat1', 'inStock:false AND price:[1 TO 300]');
$facet->createQuery('stock_pricecat2', 'inStock:true AND price:[300 TO *]');
$facet->createQuery('nostock_pricecat2', 'inStock:false AND price:[300 TO *]');

// add the facet instance to the facetset
$facetSet->addFacet($facet);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Multiquery facet counts:<br/>';
$facet = $resultset->getFacetSet()->getFacet('stock');
foreach($facet as $key => $count) {
    echo $key . ' [' . $count . ']<br/>';
}

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';
    echo '<tr><th>id</th><td>' . $document->id . '</td></tr>';
    echo '<tr><th>name</th><td>' . $document->name . '</td></tr>';
    echo '<tr><th>price</th><td>' . $document->price . '</td></tr>';
    echo '</table>';
}

htmlFooter();