if ('serviceWorker' in navigator){
    console.log("SW present !!! ");
    navigator.serviceWorker.register('sw.js', {
    }).then(function(registration){
      console.log('Service worker registered');
    })
    .catch(function(err){
    });
 }

 let deferredPrompt;
var alertPopUp=document.getElementById("alertPopup");
 window.addEventListener('beforeinstallprompt',(e)=>{
 	e.preventDefault();
 	deferredPrompt=e;
    alertPopUp.style.display='block';
 });
 window.addEventListener('appinstalled',(event)=>{
 	app.logEvent('a2hs','installed');
 });