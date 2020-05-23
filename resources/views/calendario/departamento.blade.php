<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <div class="row">
        <div class="col-md-3" id="filter_global">
            <td>busqueedad global</td>
            <td align="center"><input type="text" class="global_filter" id="global_filter"></td>
        </div>
        <div class="col-md-3" id="filter_col1" data-column="0">

                <td>Column - Name</td>
                <td align="center"><input type="text" class="column_filter" id="col0_filter"></td>
        </div>
        <div class="col-md-3" id="filter_col2" data-column="1">

            <td>Column - Position</td>
                <td align="center"><input type="text" class="column_filter" id="col1_filter"></td>
    </div>
    <div class="col-md-3" id="filter_col3" data-column="2">

        <td>Column - Office</td>
                <td align="center"><input type="text" class="column_filter" id="col2_filter"></td>
</div>
    </div>
    <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
               <th><input type="radio" name="inputR" id="i1"></th>
                <th><input type="radio" name="inputR" id="i2"></th>
                <th><input type="radio" name="inputR" id="i3"></th>
                <th><input type="radio" name="inputR" id="i4"></th>
                <th><input type="radio" name="inputR" id="i5"></th>
            </tr>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Office</th>
                <th>Age</th>
                <th>Start date</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tiger Nixon</td>
                <td>System Architect</td>
                <td>Edinburgh</td>
                <td>61</td>
                <td>2011/04/25</td>
                <td>$320,800</td>
            </tr>
            <tr>
                <td>Garrett Winters</td>
                <td>Accountant</td>
                <td>Tokyo</td>
                <td>63</td>
                <td>2011/07/25</td>
                <td>$170,750</td>
            </tr>
            <tr>
                <td>Ashton Cox</td>
                <td>Junior Technical Author</td>
                <td>San Francisco</td>
                <td>66</td>
                <td>2009/01/12</td>
                <td>$86,000</td>
            </tr>
            <tr>
                <td>Cedric Kelly</td>
                <td>Senior Javascript Developer</td>
                <td>Edinburgh</td>
                <td>22</td>
                <td>2012/03/29</td>
                <td>$433,060</td>
            </tr>
            <tr>
                <td>Airi Satou</td>
                <td>Accountant</td>
                <td>Tokyo</td>
                <td>33</td>
                <td>2008/11/28</td>
                <td>$162,700</td>
            </tr>
            <tr>
                <td>Brielle Williamson</td>
                <td>Integration Specialist</td>
                <td>New York</td>
                <td>61</td>
                <td>2012/12/02</td>
                <td>$372,000</td>
            </tr>

        </tbody>

    </table>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
function filterGlobal () {
    $('#example').DataTable().search(
        $('#global_filter').val(),

    ).draw();
}
function filterColumn ( i ) {
    $('#example').DataTable().column( i ).search(
        $('#col'+i+'_filter').val(),

    ).draw();
}

$(document).ready(function() {
    var table = $('#example').DataTable();


    $("#i1").click(function() {
        if($("#i1").is(':checked')) {
            table
            .search( '' )
            .columns().search( '' )
            .draw();
            $('#filter_global').show()
            $('#filter_col1').show();
            $('#filter_col2').hide();
            $('#filter_col3').hide();

        } else {
            alert("No está activado");
        }
    });

    $("#i2").click(function() {
        if($("#i2").is(':checked')) {
            table
            .search( '' )
            .columns().search( '' )
            .draw();
            $('#filter_global').hide()
            $('#filter_col1').hide();
            $('#filter_col2').show();
            $('#filter_col3').hide();

        } else {
            alert("No está activado");
        }
    });





    $('#example').DataTable();
    $('#filter_col1').hide();
    $('#filter_col2').hide();
    $('#filter_col3').hide();

    $('input.global_filter').on( 'keyup click', function () {
        filterGlobal();
    } );

    $('input.column_filter').on( 'keyup click', function () {
        filterColumn( $(this).parents('div').attr('data-column') );
    } );
} );
</script>
