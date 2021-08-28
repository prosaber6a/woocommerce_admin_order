var orderItems = [{product_id: 58, quantity: 1, price: "15"}];

var url = JSON.parse(action_url)[0];
console.log(url)
const productData = JSON.parse(products);
;(function ($) {
    $(document).ready(function () {
        $('#products_select_box').select2({
            placeholder: "Select a state",
            allowClear: true,
            templateResult: formatState,
            templateSelection: formatSelect,
        });

        $('#country').select2({
            placeholder: 'Select a country',
            allowClear: true,
        });
    });


    function formatState(opt) {
        if (!opt.id) {
            return opt.text;
        }

        let optimage = jQuery(opt.element).attr('data-image');
        let name = jQuery(opt.element).attr('data-name')

        if (!optimage) {
            return opt.text;
        } else {
            var $opt = jQuery(
                '<span class="flex gap-1"><img class="align-middle h-12 border-0" src="' + optimage + '" /> <span class="block"><strong>' + opt.text.toUpperCase() + '</strong><br>Name: ' + name + '</span></span>'
            );
            return $opt;
        }
    }

    function formatSelect(opt) {

        if (opt.id !== '') {
            let id = parseInt(opt.id);

            for (let i = 0; i < productData.length; i++) {
                if (productData[i].id === id) {
                    // console.log(productData[i]);
                    renderTable(productData[i]);
                    break;
                }
            }
        }

        if (orderItems.length > 0) {
            $('#orderitems_table').show();
        }

        return "Search product by SKU";
    }

    function renderTable(obj = null) {
        if (obj === null) {
            return;
        }

        let item = {product_id: obj.id, quantity: 1, price: obj.price, name: obj.name};

        orderItems[orderItems.length] = item;

        // let data = $('#orderitems_table tbody').html();
        let data = "";
        data += `
            <tr data-id="${obj.id}" class="border-b-2 border-gray-300 mb-1">
                <td class="flex gap-4 items-center m-2"><img class="h-12 border-0" src="${obj.img}"> <span class="block">${obj.name}</span></td>
                <td class="text-center">
                    <input type="number" name="quantity" onchange="update_quantity(this)" value="1" class="w-16">
                </td>
                <td class="text-center">$ ${obj.price}</td>
                <td class="text-center">
                    <button type="delete" onclick="remove_row(this)" class="deletebtn hover:text-red-700 text-red-500">Delete</button>
                </td>
            </tr>
        `;
        $('#orderitems_table tbody').append(data);
        calculate();


    }

    $('input[name=quantity]').on('change', () => {
        console.log('qty');
    });


    $('#submit').click(function () {

        // console.log(orderItems.length)
        $('#submit').attr('disabled', '');
        $('#submit').addClass('disabled:opacity-50 cursor-wait');
        $('#submit').removeClass('hover:bg-indigo-800');

        if (orderItems.length < 1) {
            alert('Please select products');

            $('#submit').removeAttr('disabled');
            $('#submit').removeClass('disabled:opacity-50 cursor-wait');
            $('#submit').addClass('hover:bg-indigo-800');

            return false;
        }

        let first_name = $('#cust_first_name').val();
        let last_name = $('#cust_last_name').val();
        let address_line = $('#address_line').val();
        let city = $('#city').val();
        let country = $('#country').val();
        let nonce = $('#nonce').val();

        let total = 0;
        orderItems.forEach((item) => {
            total += parseFloat(item.price) * parseInt(item.quantity);
        });

        if (first_name == "" || last_name == "" || address_line == "" || city == "" || country == "") {

            $('#submit').removeAttr('disabled');
            $('#submit').removeClass('disabled:opacity-50 cursor-wait');
            $('#submit').addClass('hover:bg-indigo-800');
            alert("Customer and Address info must not be empty!");
            return false;
        }

        // console.log(first_name, last_name, address_line, city, country, nonce);
        // $('form').attr('action', url);
        // $('form').attr('method', 'POST');


        $.ajax({
            url: url,
            data: {
                first_name: first_name,
                nonce: nonce,
                action: 'woo_admin_order_submit',
                last_name: last_name,
                address_line: address_line,
                city: city,
                country: country,
                product_data: orderItems,
                total: total
            },
            type: 'post',
            datatype: 'json',
            success: function (response) {
                console.log("response: "+response);
            },
            error: function (response) {
                console.log("error", response)
            }
        });
    });


})(jQuery);

function remove_row(element) {
    let id = parseInt(element.parentNode.parentElement.getAttribute('data-id'));

    orderItems = orderItems.filter((item) => {
        console.log(item)
        return item.product_id != id;
    });

    console.log(orderItems)

    element.parentNode.parentElement.remove();
    calculate();
}


function update_quantity(element) {
    // console.log('hello')
    let qty = parseInt(element.value);
    let id = parseInt(element.parentNode.parentElement.getAttribute('data-id'));
    for (let i = 0; i < orderItems.length; i++) {
        console.log(orderItems[i]);
        if (orderItems[i].product_id === id) {
            orderItems[i].quantity = qty;
            // console.log('update qty');
            break;
        }
    }

    calculate();
}

function calculate() {

    let total_price = 0;
    orderItems.forEach((item) => {
            total_price += parseFloat(item.price) * parseInt(item.quantity);
        }
    );

    jQuery('#total_price').html("$" + total_price);

}
