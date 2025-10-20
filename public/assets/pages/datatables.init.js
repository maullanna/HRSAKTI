/*
 Template Name: Veltrix - Responsive Bootstrap 4 Admin Dashboard
 Author: Themesbrand
 File: Datatable js
 */

$(document).ready(function() {
    // Initialize datatable only if element exists and not already initialized
    if ($('#datatable').length && !$.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable();
    }

    //Buttons examples - only initialize if element exists and not already initialized
    if ($('#datatable-buttons').length && !$.fn.DataTable.isDataTable('#datatable-buttons')) {
        var table = $('#datatable-buttons').DataTable({
            lengthChange: false,
            buttons: ['copy', 'excel', 'pdf', 'colvis']
        });

        table.buttons().container()
            .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
    }
} );