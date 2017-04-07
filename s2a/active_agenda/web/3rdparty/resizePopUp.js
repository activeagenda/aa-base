//file: resizePopUp.js

//these functions from www.howtocreate.co.uk
        
function getRefToDivMod( divID, oDoc ) {
    if( !oDoc ) { oDoc = document; }
    if( document.layers ) {
        if( oDoc.layers[divID] ) { return oDoc.layers[divID]; } else {
            for( var x = 0, y; !y && x < oDoc.layers.length; x++ ) {
                y = getRefToDivNest(divID,oDoc.layers[x].document); }
            return y; } }
    if( document.getElementById ) { return oDoc.getElementById(divID); }
    if( document.all ) { return oDoc.all[divID]; }
    return oDoc[divID];
}

function resizeWinTo( idOfDiv ) {
    var oH = getRefToDivMod( idOfDiv ); if( !oH ) { return false; }
    var x = window; x.resizeTo( screen.availWidth, screen.availWidth );
    var oW = oH.clip ? oH.clip.width : oH.offsetWidth;
    var oH = oH.clip ? oH.clip.height : oH.offsetHeight; if( !oH ) { return false; }
    x.resizeTo( oW + 200, oH + 200 );
    var myW = 0, myH = 0, d = x.document.documentElement, b = x.document.body;
    if( x.innerWidth ) { myW = x.innerWidth; myH = x.innerHeight; }
    else if( d && d.clientWidth ) { myW = d.clientWidth; myH = d.clientHeight; }
    else if( b && b.clientWidth ) { myW = b.clientWidth; myH = b.clientHeight; }
    if( window.opera && !document.childNodes ) { myW += 16; }
    //second sample, as the table may have resized
    var oH2 = getRefToDivMod( idOfDiv );
    var oW2 = oH2.clip ? oH2.clip.width : oH2.offsetWidth;
    var oH2 = oH2.clip ? oH2.clip.height : oH2.offsetHeight;
    
    x.resizeTo( oW2 + ( ( oW + 200 ) - myW ), oH2 + ( (oH + 200 ) - myH ) );
}