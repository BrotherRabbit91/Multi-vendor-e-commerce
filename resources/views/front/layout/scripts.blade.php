@php
    // Note: Most of this file contents were moved from front/js/custom.js to enable us to write PHP code within JavaScript code (to operate the Dynamic Filters dynamically (the second way)). This file is 'include'-ed in front/layout/layout.blade.php    // Note: In order to be able to write PHP code within JavaScript code, you must write it in a .php file (a file with .php extension) (Note that this file has a '.php' extension!) (Another way to go is using an AJAX call to get the $productFilters!)    // https://www.youtube.com/watch?v=rwj3nRYpUEk&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=90    // https://coursesweb.net/javascript/javascript-code-php    // https://www.google.com/search?q=how+to+write+php+within+javascript&sxsrf=ALiCzsbQCjQYMB12WXjmm8i6dmMSxp5cRA%3A1665267622652&ei=pvdBY6K2J5GWxc8PuY-z4A0&ved=0ahUKEwiioeqo1dH6AhURS_EDHbnHDNwQ4dUDCA4&uact=5&oq=how+to+write+php+within+javascript&gs_lcp=Cgdnd3Mtd2l6EAMyBggAEB4QCDIFCAAQhgMyBQgAEIYDMgUIABCGAzoGCAAQHhAHOggIABAeEAgQBzoICAAQgAQQiwM6BAgAEB46CggAEIAEEA0QiwM6BggAEB4QDToICAAQHhAIEA1KBAhBGABKBAhGGABQAFipHmDZJWgBcAF4AIABiwGIAZIHkgEDMC44mAEAoAEBuAECwAEB&sclient=gws-wiz
    // Operate the Dynamic Filters dynamically not statically (the second way)    // https://www.youtube.com/watch?v=rwj3nRYpUEk&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=90



    $productFilters = \App\Models\ProductsFilter::productFilters(); // Get all the (enabled/active) Filters    // (Another way to go is using an AJAX call to get the $productFilters!)
    // dd($productFilters);
@endphp


<script> // Note: We must use a <script> tag to write JavaScript because this file has a .php extension
    // Using jQuery for the website FRONT section:
    $(document).ready(function() {

        // Sorting Filter WITHOUT AJAX (using HTML <form> and jQuery) in front/products/listing.blade.php    // https://www.youtube.com/watch?v=u2NiZzjRL8U&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=80
        /*
        $('#sort').on('change', function() {
            // console.log(this);
            // console.log(this.form); // this.form means the containing <form> HTML element    // https://stackoverflow.com/questions/11042214/difference-between-this-form-and-document-forms
            this.form.submit(); // submit the <form> (if the HTML <form> "method" attribute is absent, this means the "method" is "GET")
        });
        */



        // Sorting Filter WITH AJAX in front/products/listing.blade.php. Check ajax_products_listing.blade.php (which is 'include'-ed by listing.blade.php page)    // https://www.youtube.com/watch?v=APPKmLlWEBY&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu
        $('#sort').on('change', function() { // selecting the <selec> box in listing.blade.php
            var sort = $('#sort').val(); // Get the <select> box value of the 'sort' name HTML attribute
            var url  = $('#url').val(); // Get the <input> field value of the 'url' name HTML attribute ($url is passed from listing() method in Front/ProductsController.php to view (lising.blade.php))
            // console.log(sort);
            // console.log(url);

            // Send all the 'fabric' Dynamic Filter values (the ':checked' checkboxes <input> fields values in filters.blade.php) along with the Sorting Filters 'sort'    // Check 22:19 in https://www.youtube.com/watch?v=r-NjOGA4qFw&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=91
            // var fabric = get_filter('fabric'); // get all the ':checked' checkboxes (the 'fabric' filter values) in filters.blade.php // get the filter values array of 'fabric' filter like    ['cotton', 'polyester', ...]    as an ARRAY    // get_filter() is in front/js/custom.js

            // Send all the Dynamic Filter values DYNAMICALLY (the ':checked' checkboxes <input> fields values in filters.blade.php) along with the Sorting Filters 'sort'    // Check 21:21 in https://www.youtube.com/watch?v=rwj3nRYpUEk&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=91
            @foreach ($productFilters as $filters) // A new separate loop to get all the other remaining filters' values, along with the current jQuery selected filter    // We have to loop over the main filters here AGAIN, otherwise the $(.filter) selector would select the filter values of ONE filter ONLY, and would ignore the filter values of all the other filters e.g. Without the foreach loop, it would select the 'fabric' filter values like: ['cotton', 'polyester'] but would ignore another filter like 'sleeve' filter and ignore its checked values like: ['full sleeve', 'half sleeve'] . Tip: Remove the foreach loop and change $filters to $filter and check the console (Don't forget to console.log(filter) in front/js/custom.js)
                var {{ $filters['filter_column'] }} = get_filter('{{ $filters['filter_column'] }}'); // get all the ':checked' checkboxes (all the other filter values along with current jQuery selected filter) in filters.blade.php    // get the filter values array of filter like    ['cotton', 'polyester', ...]    as an ARRAY    // get_filter() is in front/js/custom.js
            @endforeach


            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, // X-CSRF-TOKEN: https://laravel.com/docs/9.x/csrf#csrf-x-csrf-token    // Check 12:37 in https://www.youtube.com/watch?v=maEXuJNzE8M&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=16 AND Check 12:06 in https://www.youtube.com/watch?v=APPKmLlWEBY&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu
                url    : url, // e.g. /men (this url hits the Dynamic Routes in web.php using a foreach loop ('ProductsController@listing'))    // check the web.php for this route and check the ProductsController for the listing() method
                type   : 'Post',
                // data   : {sort: sort, url: url, fabric: fabric}, // we pass the 'sort' (Sorting Filter), 'url' variables and send 'fabric' Dynamic Filter along with them

                data   : { // we pass the 'sort' (Sorting Filter), 'url' variables along with the all Dynamic Filters's values DYNAMICALLY    // Check 21:21 in https://www.youtube.com/watch?v=rwj3nRYpUEk&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=91

                    @foreach ($productFilters as $filters) // A new separate loop to send all the other remaining filters' values in the AJAX call, along with sending the current jQuery selected filter's values    // We have to loop over the main filters here AGAIN, otherwise the $(.filter) selector would select the filter values of ONE filter ONLY, and would ignore the filter values of all the other filters e.g. Without the foreach loop, it would select the 'fabric' filter values like: ['cotton', 'polyester'] but would ignore another filter like 'sleeve' filter and ignore its checked values like: ['full sleeve', 'half sleeve'] . Tip: Remove the foreach loop and change $filters to $filter and check the console (Don't forget to console.log(filter) in front/js/custom.js)
                        {{ $filters['filter_column'] }}: {{ $filters['filter_column'] }}, // Note that fabric is an ARRAY of the filter values (like    ['cotton', 'polyester', ...]    ) of the 'fabric' filter    // send the Sorting Filters values (sort) along with the Dynamic Filters values ('fabric' Dynamic Filter values)
                    @endforeach
                    sort: sort, url: url

                },
                success: function(data) {
                    // alert(data);
                    // console.log(data);
                    // console.log(data.sort);
                    // console.log(data.url);
                    $('.filter_products').html(data);
                },
                error  : function() {
                    alert('Error');
                }
            });
        });

        // operate Dynamic Filters statically using the first way (for the 'fabric' filter only): // Check get_filter() function in this file and the listing() method in Front/ProductsController.php
        // We will need to send the 'url' and 'sort' to include them too just like we did with the Sorting Filter function above (in this file) (along with sending 'fabric')
        /*$('.fabric').on('click', function() { // select the 'fabric' filter (which is generated dynamically from the foreach loop) in filters.blade.php
            var url  = $('#url').val(); // from the <select> box in listing.blade.php page (which, in turn, includes filters.blade.php page)
            var sort = $('#sort option:selected').val(); // select the :selected <option> element ONLY which is :selected in listing.blade.php (which, in turn, includes filters.blade.php) (like 'price_highest', 'name_z_a', ...)    // https://www.w3schools.com/jquery/sel_input_selected.asp    // .text() https://www.w3schools.com/jquery/html_text.asp    // send the Sorting Filters values (sort) along with the Dynamic Filters values ('fabric' Dynamic Filter values)
            // console.log(sort);

            var fabric = get_filter('fabric'); // get all the ':checked' checkboxes (the 'fabric' filter values) in filters.blade.php // get the filter values array of 'fabric' filter like    ['cotton', 'polyester', ...]    as an ARRAY    // get_filter() is in front/js/custom.js


            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, // X-CSRF-TOKEN: https://laravel.com/docs/9.x/csrf#csrf-x-csrf-token    // Check 12:37 in https://www.youtube.com/watch?v=maEXuJNzE8M&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=16 AND Check 12:06 in https://www.youtube.com/watch?v=APPKmLlWEBY&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu
                url    : url, // this will hit the listing() method in Front/ProductsController.php    // e.g. /men (this url hits the Dynamic Routes in web.php using a foreach loop ('ProductsController@listing'))    // check the web.php for this route and check the ProductsController for the listing() method
                method : 'Post',
                data   : {url: url, sort: sort, fabric: fabric}, // Note that fabric is an ARRAY of the filter values (like    ['cotton', 'polyester', ...]    ) of the 'fabric' filter    // send the Sorting Filters values (sort) along with the Dynamic Filters values ('fabric' Dynamic Filter values)
                success: function(data) {
                    $('.filter_products').html(data); // in listing.blade.php
                },
                error  : function() {
                    alert('Error');
                }
            });
        });*/

        // operate Dynamic Filters Dynamically using the second way (for ALL filters): // Check get_filter() function in front/js/custom.js and the listing() method in Front/ProductsController.php    // https://www.youtube.com/watch?v=rwj3nRYpUEk&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=91
        // We will need to send the 'url' and 'sort' to include them too just like we did with the Sorting Filter function above (in this file) (along with sending 'fabric')
        @foreach ($productFilters as $filter) // get all the active/enabled filters from database ($productFilters comes from the far top of this file)

            $('.{{ $filter['filter_column'] }}').on('click', function() { // select the 'fabric' filter (which is generated dynamically from the foreach loop) in filters.blade.php
                var url  = $('#url').val(); // from the <select> box in listing.blade.php page (which, in turn, includes filters.blade.php page)
                var sort = $('#sort option:selected').val(); // select the :selected <option> element ONLY which is :selected in listing.blade.php (which, in turn, includes filters.blade.php) (like 'price_highest', 'name_z_a', ...)    // https://www.w3schools.com/jquery/sel_input_selected.asp    // .text() https://www.w3schools.com/jquery/html_text.asp    // send the Sorting Filters values (sort) along with the Dynamic Filters values ('fabric' Dynamic Filter values)
                // console.log(sort);

                
                @foreach ($productFilters as $filters) // A new separate loop to get all the other remaining filters' values, along with the current jQuery selected filter    // We have to loop over the main filters here AGAIN, otherwise the $(.filter) selector would select the filter values of ONE filter ONLY, and would ignore the filter values of all the other filters e.g. Without the foreach loop, it would select the 'fabric' filter values like: ['cotton', 'polyester'] but would ignore another filter like 'sleeve' filter and ignore its checked values like: ['full sleeve', 'half sleeve'] . Tip: Remove the foreach loop and change $filters to $filter and check the console (Don't forget to console.log(filter) in front/js/custom.js)
                    var {{ $filters['filter_column'] }} = get_filter('{{ $filters['filter_column'] }}'); // get all the ':checked' checkboxes (the 'fabric' filter values) in filters.blade.php    // get the filter values array of 'fabric' filter like    ['cotton', 'polyester', ...]    as an ARRAY    // get_filter() is in front/js/custom.js
                @endforeach



                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, // X-CSRF-TOKEN: https://laravel.com/docs/9.x/csrf#csrf-x-csrf-token    // Check 12:37 in https://www.youtube.com/watch?v=maEXuJNzE8M&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu&index=16 AND Check 12:06 in https://www.youtube.com/watch?v=APPKmLlWEBY&list=PLLUtELdNs2ZaAC30yEEtR6n-EPXQFmiVu
                    url    : url, // this will hit the listing() method in Front/ProductsController.php    // e.g. /men (this url hits the Dynamic Routes in web.php using a foreach loop ('ProductsController@listing'))    // check the web.php for this route and check the ProductsController for the listing() method
                    method : 'Post',
                    // data   : {  url: url, sort: sort,  {{ $filter['filter_column'] }}: {{ $filter['filter_column'] }}  }, // Note that fabric is an ARRAY of the filter values (like    ['cotton', 'polyester', ...]    ) of the 'fabric' filter    // send the Sorting Filters values (sort) along with the Dynamic Filters values ('fabric' Dynamic Filter values)
                    data   : {

                        @foreach ($productFilters as $filters) // A new separate loop to send all the other remaining filters' values in the AJAX call, along with sending the current jQuery selected filter's values    // We have to loop over the main filters here AGAIN, otherwise the $(.filter) selector would select the filter values of ONE filter ONLY, and would ignore the filter values of all the other filters e.g. Without the foreach loop, it would select the 'fabric' filter values like: ['cotton', 'polyester'] but would ignore another filter like 'sleeve' filter and ignore its checked values like: ['full sleeve', 'half sleeve'] . Tip: Remove the foreach loop and change $filters to $filter and check the console (Don't forget to console.log(filter) in front/js/custom.js)
                            {{ $filters['filter_column'] }}: {{ $filters['filter_column'] }}, // Note that fabric is an ARRAY of the filter values (like    ['cotton', 'polyester', ...]    ) of the 'fabric' filter    // send the Sorting Filters values (sort) along with the Dynamic Filters values ('fabric' Dynamic Filter values)
                        @endforeach
                        url: url, sort: sort

                    },
                    success: function(data) {
                        $('.filter_products').html(data); // in listing.blade.php
                    },
                    error  : function() {
                        alert('Error');
                    }
                });
            });

        @endforeach

    });
</script>