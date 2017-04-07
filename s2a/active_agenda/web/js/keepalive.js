// Configure refresh interval (in seconds)
//var refreshinterval=SESSION_DEFAULT_TIMEOUT*60-30

// Do not edit the code below
var starttime
var nowtime
var secondssinceloaded

function KeepaliveRequestor() {
    var self = this;  // workaround for scope errors: see http://www.crockford.com/javascript/private.html

    /**
     *  Create XMLHttpRequest object: handles branching between
     *  versions of IE and other browers.  Inital version from:
     *  http://jibbering.com/2002/4/httprequest.html (GREAT resource)
     *
     *  later version adapted from:
     *  http://jpspan.sourceforge.net/wiki/doku.php?id=javascript:xmlhttprequest:behaviour:httpheaders
     *
     *  @return     the XMLHttpRequest object
     */
    this.getXMLHTTP = function() {
        var xmlHTTP = null;

        try {
            xmlHTTP = new XMLHttpRequest();
        } catch (e) {
            try {
                xmlHTTP = new ActiveXObject("Msxml2.XMLHTTP")
            } catch(e) {
                var success = false;
                var MSXML_XMLHTTP_PROGIDS = new Array(
                    'Microsoft.XMLHTTP',
                    'MSXML2.XMLHTTP',
                    'MSXML2.XMLHTTP.5.0',
                    'MSXML2.XMLHTTP.4.0',
                    'MSXML2.XMLHTTP.3.0'
                );
                for (var i=0;i < MSXML_XMLHTTP_PROGIDS.length && !success; i++) {
                    try {
                        xmlHTTP = new ActiveXObject(MSXML_XMLHTTP_PROGIDS[i]);
                        success = true;
                    } catch (e) {
                        xmlHTTP = null;
                    }
                }
            }

        }
        self._XML_REQ = xmlHTTP;
        return self._XML_REQ;
    }

    /**
     *   Starts the request for a url.  XMLHttpRequest will call
     *   the default callback method when the request is complete
     *   @param     url     the URL to request: absolute or relative will work
     *   @return    true
     */
    this.getURL = function(url) {
        self.userModifiedData = "";  // clear user modified data;

		// CLEAR OUT ANY CURRENTLY ACTIVE REQUESTS
            if ((typeof self._XML_REQ.abort) != "undefined" && self._XML_REQ.readyState!=0) { // Opera can't abort().
                self._XML_REQ.abort();
            }

        // MAKE THE REQUEST

            self._XML_REQ.open("GET", url, true);
	    if ((typeof self._XML_REQ.setRequestHeader) != "undefined") { // Opera can't setRequestHeader()
                self._XML_REQ.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            }
            self._XML_REQ.send("");

       return true;
    }

    // ENSURE THAT WE'VE GOT AN XMLHttpRequest OBJECT AVALIABLE
    if (!this.getXMLHTTP()) {
        throw new Error("Could not load XMLHttpRequest object");
    }
}
    

function timestart() {
	starttime=new Date()
	starttime=starttime.getTime()
    keepalive()
}

function keepalive() {
	nowtime= new Date()
	nowtime=nowtime.getTime()
	secondssinceloaded=(nowtime-starttime)/1000
	if (refreshinterval>=secondssinceloaded) {
        var timer=setTimeout("keepalive()",1000)		
    }
    else {
        clearTimeout(timer)
		var kareq = new KeepaliveRequestor()
		kareq.getURL('/keep_alive.php')
		timestart()		
    } 
}
window.onload=timestart
