var orderItems = [];

var url = JSON.parse(action_url)[0];
console.log(url)
const productData = JSON.parse(products);
;(function ($) {
    $(document).ready(function () {


        $('#country').select2({
            placeholder: 'Select a country',
            allowClear: true,
        });
    });

    $('#submit').click(function () {

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
        let email = $('#cust_email').val();
        let phone = $('#cust_phone').val();
        let nonce = $('#nonce').val();

        let total = 0;
        orderItems.forEach((item) => {
            total += parseFloat(item.price) * parseInt(item.quantity);
        });

        if (first_name == "" || last_name == "" || address_line == "" || city == "" || country == "" || email == "" || phone == "") {

            $('#submit').removeAttr('disabled');
            $('#submit').removeClass('disabled:opacity-50 cursor-wait');
            $('#submit').addClass('hover:bg-indigo-800');
            alert("Customer and Address info must not be empty!");
            return false;
        }


        $.ajax({
            url: url,
            data: {
                first_name: first_name,
                nonce: nonce,
                action: 'woo_admin_order_submit',
                last_name: last_name,
                email: email,
                phone: phone,
                address_line: address_line,
                city: city,
                country: country,
                product_data: orderItems,
                total: total
            },
            type: 'post',
            datatype: 'json',
            success: function (response) {

                $('#form')[0].reset();
                alert('Successfully Order inserted.');

                location.reload();
            }
        });
    });


    //sku search


    $('#product-sku-search').on('keyup', () => {

        $('#search-container').addClass('hidden');

        let data = '';
        let sku = $('#product-sku-search').val().toUpperCase();

        if (sku !== '') {
            let arr = null;

            for (let i = 0; i < productData.length; i++) {
                arr = productData[i];


                if (productData && arr.sku.indexOf(sku) != 0) continue;


                data += `
                <li onclick="renderTable(${arr.id})" class="hover:bg-blue-500 hover:text-white p-2">
                                    
                    <div class="flex gap-2">
                        <img class="w-16" src="${arr.img}" alt="">
                        <div class="text-left">
                            <p>SKU: <strong>${arr.sku}</strong></p>
                            <p>${arr.name}</p>
                        </div>
                    </div>
                    
                </li>
                `;

            }


        }

        if (data != '') {
            $('#search-result').html(data);
            $('#search-container').removeClass('hidden');
        }


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


function renderTable(id = -1) {
    console.log(id);

    id = parseInt(id);

    for (let i = 0; i < productData.length; i++) {
        if (productData[i].id === id) {
            obj = productData[i];
        }
    }


    if (obj === null) {
        return;
    }

    let item = {product_id: obj.id, quantity: 1, price: obj.price, name: obj.name};

    orderItems[orderItems.length] = item;

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
    jQuery('#search-container').addClass('hidden')
    jQuery('#orderitems_table tbody').append(data);
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
