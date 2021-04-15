<?php
  
class Paginator {
  
     private $_conn;
        private $_limit;
        private $_page;
        private $_query;
        private $_total;
  
}
?>

<?php
  
public function __construct( $conn, $query ) {
      
    $this->_conn = $conn;
    $this->_query = $query;
  
    $rs= $this->_conn->query( $this->_query );
    $this->_total = $rs->num_rows;
      
}

<?php
public function getData( $limit = 10, $page = 1 ) {
      
    $this->_limit   = $limit;
    $this->_page    = $page;
  
    if ( $this->_limit == 'all' ) {
        $query      = $this->_query;
    } else {
        $query      = $this->_query . " LIMIT " . ( ( $this->_page - 1 ) * $this->_limit ) . ", $this->_limit";
    }
    $rs             = $this->_conn->query( $query );
  
    while ( $row = $rs->fetch_assoc() ) {
        $results[]  = $row;
    }
  
    $result         = new stdClass();
    $result->page   = $this->_page;
    $result->limit  = $this->_limit;
    $result->total  = $this->_total;
    $result->data   = $results;
  
    return $result;
}

<?php
public function createLinks( $links, $list_class ) {
    if ( $this->_limit == 'all' ) {
        return '';
    }
  
    $last       = ceil( $this->_total / $this->_limit );
  
    $start      = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
    $end        = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;
  
    $html       = '<ul class="' . $list_class . '">';
  
    $class      = ( $this->_page == 1 ) ? "disabled" : "";
    $html       .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . ( $this->_page - 1 ) . '">&laquo;</a></li>';
  
    if ( $start > 1 ) {
        $html   .= '<li><a href="?limit=' . $this->_limit . '&page=1">1</a></li>';
        $html   .= '<li class="disabled"><span>...</span></li>';
    }
  
    for ( $i = $start ; $i <= $end; $i++ ) {
        $class  = ( $this->_page == $i ) ? "active" : "";
        $html   .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . $i . '">' . $i . '</a></li>';
    }
  
    if ( $end < $last ) {
        $html   .= '<li class="disabled"><span>...</span></li>';
        $html   .= '<li><a href="?limit=' . $this->_limit . '&page=' . $last . '">' . $last . '</a></li>';
    }
  
    $class      = ( $this->_page == $last ) ? "disabled" : "";
    $html       .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . ( $this->_page + 1 ) . '">&raquo;</a></li>';
  
    $html       .= '</ul>';
  
    return $html;
}


<!DOCTYPE html>
    <head>
        <title>PHP Pagination</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
                <div class="col-md-10 col-md-offset-1">
                <h1>PHP Pagination</h1>
                <table class="table table-striped table-condensed table-bordered table-rounded">
                        <thead>
                                <tr>
                                <th>City</th>
                                <th width="20%">Country</th>
                                <th width="20%">Continent</th>
                                <th width="25%">Region</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                </table>
                </div>
        </div>
        </body>
</html>

<?php for( $i = 0; $i < count( $results->data ); $i++ ) : ?>
        <tr>
                <td><?php echo $results->data[$i]['Name']; ?></td>
                <td><?php echo $results->data[$i]['Country']; ?></td>
                <td><?php echo $results->data[$i]['Continent']; ?></td>
                <td><?php echo $results->data[$i]['Region']; ?></td>
        </tr>
<?php endfor; ?>

<?php
    require_once 'Paginator.class.php';
  
    $conn       = new mysqli( '127.0.0.1', 'root', 'root', 'world' );
  
    $limit      = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : 25;
    $page       = ( isset( $_GET['page'] ) ) ? $_GET['page'] : 1;
    $links      = ( isset( $_GET['links'] ) ) ? $_GET['links'] : 7;
    $query      = "SELECT City.Name, City.CountryCode, Country.Code, Country.Name AS Country, Country.Continent, Country.Region FROM City, Country WHERE City.CountryCode = Country.Code";
  
    $Paginator  = new Paginator( $conn, $query );
  
    $results    = $Paginator->getData( $limit, $page );
?>

<?php for( $i = 0; $i < count( $results->data ); $i++ ) : ?>
        <tr>
                <td><?php echo $results->data[$i]['Name']; ?></td>
                <td><?php echo $results->data[$i]['Country']; ?></td>
                <td><?php echo $results->data[$i]['Continent']; ?></td>
                <td><?php echo $results->data[$i]['Region']; ?></td>
        </tr>
<?php endfor; ?>

<?php echo $Paginator->createLinks( $links, 'pagination pagination-sm' ); ?>

<!DOCTYPE html>
    <head>
        <title>PHP Pagination</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
    </head>
 
    <body>
        <div class="container">
                <div class="col-md-10 col-md-offset-1">
                    <h1>PHP Pagination</h1>
                    <div id="ajax_wrapper">
                        <?php
                            require_once 'ajax_pagination.php';
                        ?>
                    </div>
                </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
        <script src="pagination.js"></script>
    </body>
</html>

<?php
require_once 'paginator.php';
  
$conn       = new mysqli( '127.0.0.1', 'root', 'root', 'world' );
 
$limit      = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : 25;
$page       = ( isset( $_GET['page'] ) ) ? $_GET['page'] : 1;
$links      = ( isset( $_GET['links'] ) ) ? $_GET['links'] : 7;
$query      = "SELECT City.Name, City.CountryCode, Country.Code, Country.Name AS Country, Country.Continent, Country.Region FROM City, Country WHERE City.CountryCode = Country.Code";
 
$Paginator  = new Paginator( $conn, $query );
$results    = $Paginator->getData( $limit, $page );
?>
 
<table class="table table-striped table-condensed table-bordered table-rounded">
    <thead>
        <tr>
            <th>City</th>
            <th width="20%">Country</th>
            <th width="20%">Continent</th>
            <th width="25%">Region</th>
        </tr>
    </thead>
    <tbody>
    <?php for( $i = 0; $i < count( $results->data ); $i++ ) : ?>
        <tr>
                <td><?php echo $results->data[$i]['first_name']; ?></td>
                <td><?php echo $results->data[$i]['last_name']; ?></td>
                <td><?php echo $results->data[$i]['email']; ?></td>
                <td><?php echo $results->data[$i]['phone']; ?></td>
        </tr>
    <?php endfor; ?>
    </tbody>
</table>
<?php echo $Paginator->createLinks( $links, 'pagination pagination-sm' ); ?>

$(document).on( "click", ".pagination a", function(e) {
    var pageValue = $(this).attr("data-page");
 
    $.ajax({
        url: '/ajax_pagination.php?limit=25&page='+pageValue,
        type: "GET",
        success: function(data){
            $("#ajax_wrapper").html(data); 
        }
    });
 
    e.preventDefault();
});

public function createLinks( $links, $list_class ) {
    if ( $this->_limit == 'all' ) {
        return '';
    }
  
    $last       = ceil( $this->_total / $this->_limit );
  
    $start      = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
    $end        = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;
  
    $html       = '<ul class="' . $list_class . '">';
  
    $class      = ( $this->_page == 1 ) ? "disabled" : "";
    $html       .= '<li class="' . $class . '"><a data-page="' . ( $this->_page - 1 ) . '" href="?limit=' . $this->_limit . '&page=' . ( $this->_page - 1 ) . '">&laquo;</a></li>';
  
    if ( $start > 1 ) {
        $html   .= '<li><a data-page="1" href="?limit=' . $this->_limit . '&page=1">1</a></li>';
        $html   .= '<li class="disabled"><span>...</span></li>';
    }
  
    for ( $i = $start ; $i <= $end; $i++ ) {
        $class  = ( $this->_page == $i ) ? "active" : "";
        $html   .= '<li class="' . $class . '"><a  data-page="' . $i . '" href="?limit=' . $this->_limit . '&page=' . $i . '">' . $i . '</a></li>';
    }
  
    if ( $end < $last ) {
        $html   .= '<li class="disabled"><span>...</span></li>';
        $html   .= '<li><a data-page="' . $last . '"href="?limit=' . $this->_limit . '&page=' . $last . '">' . $last . '</a></li>';
    }
  
    $class      = ( $this->_page == $last ) ? "disabled" : "";
    $html       .= '<li class="' . $class . '"><a data-page="' . ( $this->_page + 1 ) . '" href="?limit=' . $this->_limit . '&page=' . ( $this->_page + 1 ) . '">&raquo;</a></li>';
  
    $html       .= '</ul>';
  
    return $html;
}

