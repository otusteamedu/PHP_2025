delete_btns = document.getElementsByClassName('delete-from-cart-btn');
// console.log(delete_btns);
Array.from(delete_btns).forEach(x=>{

    x.addEventListener('click',(e)=>{

        // console.log(e.target.dataset['id_cart']);
        deleteItemHandler(e.target.dataset['id_cart'], e.target.dataset['id_product'])
            .then((res)=> {

                console.log(res.deleted,res.count);
                item = document.getElementById(res.deleted);
                if(res.count === 0){

                    item.style.display = 'none';
                    document.getElementById('cart_quantity').innerText -=1;
                }else{

                    item.querySelector('.quantity').innerText = res.count;
                }
                // document.getElementById(res.deleted).style.display = 'none';
                // document.getElementById('cart_quantity').innerHTML = document.getElementById('cart_quantity').innerHTML - 1;
            });
    })
});
async function deleteItemHandler(id_cart, id_product){

    response = await fetch("/api/deleteFromCart/?id_cart="+id_cart+"&id_product=" + id_product/*, {

        method: 'POST',
        headers: {
            'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
            'Content-Type': 'multipart/form-data'
        },
        body: JSON.stringify({"id_cart":id_cart})
    }*/);
    // response = await fetch("/api/deleteFromCart/?id_cart="+id_cart/*, {
    //
    //     method: 'POST',
    //     headers: {
    //         // "Content-Type": "application/json"
    //     },
    //     body: JSON.stringify({"id_cart":id_cart})
    // }*/);

    response = await response.json();
    return response;
}