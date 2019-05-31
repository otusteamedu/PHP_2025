delete_btns = document.getElementsByClassName('delete-from-cart-btn');
console.log(delete_btns);
Array.from(delete_btns).forEach(x=>{

    x.addEventListener('click',(e)=>{
        console.log(e.target.dataset['id_cart']);
        deleteItemHadler(e.target.dataset['id_cart'])
            .then((res)=> {

                document.getElementById(res.deleted).style.display = 'none';
                document.getElementById('cart_quantity').innerHTML = document.getElementById('cart_quantity').innerHTML - 1;
            });
    })
});
async function deleteItemHadler(id_cart){

    response = await fetch("/api/deleteFromCart/?id_cart="+id_cart/*, {

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