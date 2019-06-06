selectors = document.getElementsByClassName("status");
Array.from(selectors).forEach(x=>x.addEventListener('change',(e)=>{

    id=e.target.options[e.target.selectedIndex].dataset.orderid;
    // console.log(e.target.options[e.target.selectedIndex].dataset.orderid);
    // console.log(e.target.options[e.target.selectedIndex].dataset.id);
    // console.log(e.target.options[e.target.selectedIndex].value);

    newstatus = e.target.options[e.target.selectedIndex].value;

    // console.log(newstatus,id);
    setStatus(id,newstatus).then(res=>{

        // console.log(res);
    })
}));

async function setStatus(id, newstatus){

    // console.log(id);
    response = await fetch("/api/setOrderStatus/?id="+id+"&new_status=" + newstatus);
    // console.log("/api/showOrder/"+id);
    //     fetch("/api/setOrderStatus/"+id + "/?new_status=" +newstatus).
    //     then(data => data.json()).
    //     then(res=>console.log(res));
    data = await response.json();


    return data;
}