function myToast(status,message){
  var x = document.getElementById("toast");
    if(status == 1){
       x.style.backgroundColor='green';
       x.innerHTML=message;
    }else{
      x.style.backgroundColor='red';
      x.innerHTML=message;
    }
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 4000);
}