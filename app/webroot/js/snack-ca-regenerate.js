function sleep(ms) {
  	return new Promise(resolve => setTimeout(resolve, ms));
}

function generateCA() {
	console.log("generateCA");
	countryName = document.getElementById("ParameterCountryName").value;
	stateOrProvinceName = document.getElementById("ParameterStateOrProvinceName").value;
	localityName = document.getElementById("ParameterLocalityName").value;
	organizationName = document.getElementById("ParameterOrganizationName").value;
	console.log(countryName+" "+stateOrProvinceName+" "+localityName+" "+organizationName);
	var url = "regenerateCA";
	$("#loadingmsg").html("Please wait ... you will be redirected in 15 seconds.");
	$("#loadingicon").html("<i class='fa fa-circle-o-notch fa-spin fa-2x'></i><br><br>");
	$.post( url, { countryName: countryName,stateOrProvinceName: stateOrProvinceName, localityName: localityName, organizationName: organizationName })
        .done(function( data ) {
        console.log(data);
       	console.log("DONE");
    }).fail(function() {
        $("#alert-ca").html('<div class="alert alert-danger" role="alert">Erreur</div>');
        console.log("ERR");
    });
    
    /*console.log("DONE");
    	setTimeout(function(){ 
    	console.log('Redirect');
    	window.location.href = "/radusers";
    }, 15000);  */
}