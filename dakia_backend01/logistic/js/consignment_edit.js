		function PostcodeAnywhere_Interactive_FindByParts_v1_00Begin(Key, Organisation, Building, Street, Locality, Postcode, 						        UserName)
   		{

	 		//  document.bookingForm.company.value = '';
			//  document.bookingForm.address_line_1.value = '';
			//  document.bookingForm.address_line_2.value = '';
			//	document.bookingForm.address_line_3.value = '';

			//	document.bookingForm.city.value = '';
			//	document.bookingForm.postcode.value = '';

			var scriptTag = document.getElementById("PCAf84f3b53776c481f8a0d00ba6c342422");
			var headTag = document.getElementsByTagName("head").item(0);
			var strUrl = "";

			//Build the url
			Key = "KK47-GX49-RY18-WB49";
			UserName = "INDIV67408";
			strUrl = "https://services.postcodeanywhere.co.uk/PostcodeAnywhere/Interactive/FindByParts/v1.00/json.ws?";
			strUrl += "&Key=" + encodeURI(Key);
			//  strUrl += "&Organisation=" + encodeURI(Organisation);
			//   strUrl += "&Building=" + encodeURI(Building);
			//   strUrl += "&Street=" + encodeURI(Street);
			//   strUrl += "&Locality=" + encodeURI(Locality);
		   strUrl += "&Postcode=" + encodeURI(Postcode);
		   strUrl += "&UserName=" + encodeURI(UserName);
		   strUrl += "&CallbackFunction=PostcodeAnywhere_Interactive_FindByParts_v1_00End";

		   //Make the request
		   if (scriptTag)
		   {
			 try
			 {
			   headTag.removeChild(scriptTag);
			 }
			 catch (e)
			 {
				  //Ignore
			 }
		   }
		   scriptTag = document.createElement("script");
		   scriptTag.src = strUrl
		   scriptTag.type = "text/javascript";
		   scriptTag.id = "PCAf84f3b53776c481f8a0d00ba6c342422";
		   headTag.appendChild(scriptTag);
        }

		function PostcodeAnywhere_Interactive_FindByParts_v1_00End(response)
        {
	   	  document.getElementById('return').options.length = 0;

		  //Test for an error
		  if (response.length==1 && typeof(response[0].Error) != 'undefined')
		  {
			//Show the error message
			alert(response[0].Description);
		  }
          
		  else
          {
            //Check if there were any items found
            if (response.length==0)
            {
              alert("Sorry, no matching items found");
            }
            else
            {
              if (response.length==1)
			  {
			    PostcodeAnywhere_Interactive_RetrieveById_v1_10Begin('AA11-AA11-AA11-AA11', response[0].Id, '', '');
			  }
			  else
			  {
			    document.getElementById('return').style.display = '';
                for (var i=0;i<response.length;i++)
	            document.getElementById('return').options.add(new Option(response[i].StreetAddress + ", " + response[i].Place, response[i].Id));
	    	  }
            }
         }
      }// END FUNCTION



   function PostcodeAnywhere_Interactive_RetrieveById_v1_10Begin(Key, Id, PreferredLanguage, UserName)
   {
      var scriptTag = document.getElementById("PCAa73f9bc2b60d4e4cbd595512478a3291");
      var headTag = document.getElementsByTagName("head").item(0);
      var strUrl = "";

      Key = "KK47-GX49-RY18-WB49";
      UserName = "INDIV67408";

      //Build the url
      strUrl = "https://services.postcodeanywhere.co.uk/PostcodeAnywhere/Interactive/RetrieveById/v1.10/json.ws?";
      strUrl += "&Key=" + encodeURI(Key);
      strUrl += "&Id=" + encodeURI(Id);
      strUrl += "&PreferredLanguage=" + encodeURI(PreferredLanguage);
      strUrl += "&UserName=" + encodeURI(UserName);
      strUrl += "&CallbackFunction=PostcodeAnywhere_Interactive_RetrieveById_v1_10End";

      //Make the request
      if (scriptTag)
      {
            try
            {
                 headTag.removeChild(scriptTag);
            }
            catch (e)
            {
                 //Ignore
            }
      }
      scriptTag = document.createElement("script");
      scriptTag.src = strUrl
      scriptTag.type = "text/javascript";
      scriptTag.id = "PCAa73f9bc2b60d4e4cbd595512478a3291";
      headTag.appendChild(scriptTag);
   }

      function PostcodeAnywhere_Interactive_RetrieveById_v1_10End(response)
      {
      //Test for an error
     	 if (response.length==1 && typeof(response[0].Error) != 'undefined')
         {
            //Show the error message
            alert(response[0].Description);
         }
      	 else
         {
            //Check if there were any items found
            if (response.length==0)
               {
                  alert("Sorry, no matching items found");
               }
            else
               {
			       var retstring = response[0].Company + "<br>" + response[0].Line1 + "<br>" + response[0].Line2 + "<br>" + response[0].Line3 + "<br>" + response[0].PostTown + "<br>" + response[0].County + "<br>" + response[0].Postcode;


			       document.bookingForm.company.value = response[0].Company;
				   document.bookingForm.address_line_1.value = response[0].Line1;
				   document.bookingForm.address_line_2.value = response[0].PostTown;
				   document.bookingForm.address_line_3.value = response[0].Line3;

				   document.bookingForm.city.value = response[0].County;
				   document.bookingForm.postcode.value = response[0].Postcode;

			       document.getElementById('return').style.display = 'none';
               }
         }
   }
   
   function getCountry(country, region)
   {
	  // alert("Sorry, no matching items found");
	 $.post( "../main/ajaxlabel.php",{action:'ROUTINGCOUNTRY',country:country,region:region}, function( data ) {
				$('#country').html(data);
			});  
	   
   }

	function populateInvoice()
	{
		document.getElementById("add_perform").checked = false;
		$("#performalist").hide();
	}

	function populateCountries(servicename,country){
		
		
		var elementVal	=	document.getElementById('both_checked');
		if(elementVal !=null && document.getElementById('both_checked').checked === false &&  servicename!= '')
		{
			
			$.post( "../main/ajaxlabel.php",{servicename:servicename,country:country, action:'COUNTRYPOPULATION'}, function( data ) {
				$('#country').html(data);
			});
		}else if(elementVal == null && $('service').val() !='' && $("service_type").val() != '')
		{
			//alert(servicename);
			$.post( "../main/ajaxlabel.php",{servicename:servicename,country:country, action:'COUNTRYPOPULATION'}, function( data ) {
				$('#country').html(data);
			});
		}
		else
		{
			
			//servicename = $('#service').val();
			
			$.post( "../main/ajaxlabel.php",{regionname:servicename,country:country,action:'COUNTRYREGION'}, function( data ) {
				$('#country').html(data);
			});
		}
	}


	function serviceInformation(service){
		$.post( "../main/ajaxlabel.php",{servicename:service, action:'SERVICEINFORMATIONPOPULATE'}, function( data )
		{
			$('#service-information').html(data);
		});
	}

            function enabledisabletext(target)

            {

                    var x=document.getElementById("service");
                    var y=document.getElementById("service_type");
                    $test="not domestic";
                    $test2="not domestic";
                    //x.disabled=true
                     //alert(x.value);
                     if(x.value=='DBP')

                        {
                        var x=document.getElementById("service");
                        var y=document.getElementById("service_type");
                        var test="domestic";
                        //alert("inside DBP");
                        y.disabled=false;

                        }

                         if(x.value=='R1')

                        {
                        var x=document.getElementById("service");
                        var y=document.getElementById("service_type");
                        var test2="not domestic";
                        //alert("inside non DBP");

                         y.disabled=true;
                        }

            }

            function changeClass()
            {
               var y=document.getElementById("service_type");
               //alert(y.value);
		       if (y.value=="WPX")
        		document.getElementById("value").className = "clsMandatory";
            }
			
	function enableYodel()
	{
		var y=document.getElementById("service_type").value;
		
		
		var yodel	=	y.match(/yodel/gi);
		if (yodel != null)
		{
			$('#yodelextra').show();
		}
		else
		{
			$('#yodelextra').hide();	
		}
	}				
			
	function enableDim()
	{
		
		var y=document.getElementById("service_type").value;
		//alert(y.value);
		var resta			=	y.match(/dhl/gi);
		var restb			=	y.match(/fedex/gi);
		var restups			=	y.match(/ups/gi);
		var restpalletways	=	y.match(/palletways/gi);
		if (resta != null || restb != null || restups != null )
		{
			document.getElementById("add_dim").readonly='';
			document.getElementById("add_dim").disabled ='';
			document.getElementById("add_perform").readonly='';
			document.getElementById("add_perform").disabled ='';
			$('#palletwaysextras').hide();
			//$('#numberpiecesdiv').show();
		}
		else if(restpalletways != null )
		{
			$('#palletwaysextras').show();
			document.getElementById("add_dim").checked=false;
			document.getElementById("add_dim").readonly='readonly';
			document.getElementById("add_dim").disabled ='disabled';
			document.getElementById("add_perform").checked=false;
			document.getElementById("add_perform").readonly='readonly';
			document.getElementById("add_perform").disabled ='disabled';
			$('#someListToAlter').hide();
		}
		
		else
		{
			$('#palletwaysextras').hide();
			document.getElementById("add_dim").checked=false;
			document.getElementById("add_dim").readonly='readonly';
			document.getElementById("add_dim").disabled ='disabled';
			document.getElementById("add_perform").checked=false;
			document.getElementById("add_perform").readonly='readonly';
			document.getElementById("add_perform").disabled ='disabled';
			$('#someListToAlter').hide();
			
		}
	}
		function noSpeciatCharacter(e){
			var unicode=e.charCode? e.charCode : e.keyCode
			//alert(unicode);
			if (unicode!=8)
			{
				if ((unicode == 31) ||(unicode >= 33&&unicode<=35) ||(unicode >= 39&&unicode<=43) ||  (unicode >= 36&&unicode<=38) || unicode== 163|| unicode== 94|| unicode== 64|| unicode== 126) //if not a number
					return false //disable key press
			}
		}
	

            function checkpostcode()
            {

                    var y=document.getElementById("country");
                    var country2= new Array("AFGHANISTAN","ALBANIA","AMERICAN SAMOA","ANDORRA","ANGOLA","ANGUILLA","ANTARCTICA","ANTIGUA AND BARBUD","ARUBA","BAHAMAS","BAHRAIN","BARBADOS","BELIZE","BENIN","BERMUDA","BHUTAN","BOLIVIA","BOUVET ISLAND","BRITISH INDIAN OCEAN TERRITORY","BURKINA FASO","BURUNDI","CAMBODIA","CAMEROON","CAPE VERDE","CAYMAN ISLANDS","CENTRAL AFRICAN REPUBLIC","CHAD","CHILE","CHRISTMAS ISLAND","COCOS (KEELING) ISLANDS","COLOMBIA","COMOROS","CONGO","CONGO, THE DEMOCRATIC REPUBLIC OF THE","COOK ISLANDS","COSTA RICA","COTE D'IVOIRE","DJIBOUTI","DOMINICA","DOMINICAN REPUBLIC","ECUADOR","EGYPT","EL SALVADOR","EQUATORIAL GUINEA","ERITREA","ETHIOPIA","FALKLAND ISLANDS (MALVINAS)","FAROE ISLANDS","FIJI","FRENCH GUIANA","FRENCH POLYNESIA","FRENCH SOUTHERN TERRITORIES","GABON","GAMBIA","GHANA","GIBRALTAR","GREENLAND","GRENADA","GUATEMALA","GUINEA","GUINEA-BISSAU","GUYANA","HAITI","HEARD ISLAND AND MCDONALD ISLANDS","HOLY SEE (VATICAN CITY STATE)","HONDURAS","HONG KONG","IRAN,ISLAMIC REPUBLIC OF","IRAQ","IRELAND","JAMAICA","JORDAN","KIRIBATI","KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF","KUWAIT","LAO PEOPLE'S DEMOCRATIC REPUBLIC","LEBANON","LESOTHO","LIBERIA","LIBYAN ARAB JAMAHIRIYA","MACAO","MALAWI","MALI","MALTA","MARSHALL ISLANDS","MAURITANIA","MAURITIUS","MAYOTTE","MICRONESIA, FEDERATED STATES OF","MONTSERRAT","MOZAMBIQUE","MYANMAR","NAMIBIA","NAURU","NEPAL","NETHERLANDS ANTILLES","NEW CALEDONIA","NICARAGUA","NIGER","NIGERIA","NIUE","NORFOLK ISLAND","NORTHERN MARIANA ISLANDS","OMAN","PALAU","PALESTINIAN TERRITORY,OCCUPIED","PANAMA","PAPUA NEW GUINEA","PARAGUAY","PERU","PITCAIRN","QATAR","RWANDA","SAINT HELENA","SAINT KITTS AND NEVIS","SAINT LUCIA","SAINT PIERRE AND MIQUELON","SAINT VINCENT AND THE GRENADINES","SAMOA","SAN MARINO","SAO TOME AND PRINCIPE","SAUDI ARABIA","SENEGAL","SEYCHELLES","SIERRA LEONE","SOLOMON ISLANDS","SOMALIA","SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS","SRILANKA","SUDAN","SURINAME","SVALBARD AND JAN MAYEN","SWAZILAND","SYRIAN ARAB REPUBLIC","TANZANIA, UNITED REPUBLIC OF","TIMOR-LESTE","TOGO","TOKELAU","TONGA","TRINIDAD AND TOBAGO","TURKS AND CAICOS ISLANDS","TUVALU","UGANDA","UNITED ARAB EMIRATES","UNITED STATES MINOR OUTLYING ISLANDS","VANUATU","VIET NAM","VIRGIN ISLANDS, BRITISH","WALLIS AND FUTUNA","WESTERN SAHARA","YEMEN","ZAMBIA","ZIMBABWE","BONAIRE","BONAIRE","GUERNESY","SAINT MARTIN");
//alert(y.value);
var tt=y.value;
var check=false;
// alert(tt);
for(var i=0; i<country2.length; i++) {
    if (country2[i] == tt)
    {
//alert("found");
check=true;
//document.getElementById('divPostCode').hide();
//document.getElementById('test1').innerHTML = "";

    }
  }

  //alert(check);
  if(check==false)
  {
     // alert("inside check false");
      //var html= "<strong> Post Code </strong><input type='text' name='postcode' id='postcode' size='20'/>";
      //document.getElementById('test2').innerHTML = html;
      ///$('#divPostCode').show();
  }
                 }

    function changeZipCode()
                {

                    var y=document.getElementById("country");
                    //alert(y.value);
        if (y.value=="WPX")
        document.getElementById("value").className = "clsMandatory";
                 }


            function removeAllOptions(selectbox)
            {
              var i;
              for(i=selectbox.options.length-1;i>=0;i--)
               {
        //selectbox.options.remove(i);
         selectbox.remove(i);
                 }
}

  function addOption(selectbox, value, text )
{
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;

    selectbox.options.add(optn);
}


		$(document).ready(function(){
			$("#btnAddress").click(function(){
				$("#form_action").val("Address");
				$("#bookingForm").submit();
			})
			
			//sender checked function
			$("#sender_checked").change(function() {
				$("#form_action").val("service");
				$("#bookingForm").submit();
			});
			// Service changes
			$("#service").change(function() {

				$("#service_type").val("");
				$("#form_action").val("service");								
				//$("#form_action").val("ROUTINGCOUNTRY");
				
				$("#bookingForm").submit();
			});
			
	$(".osx").bind("click", function(){
							
						OSX.init();
						
					});
			
			

// Service changes
			$("#both_checked").change(function() {


				$("#form_action").val("service");
				
				$("#bookingForm").submit();
			});


			// number of pieces changes
			/*$("#number_pieces").change(function() {
				// only matters for international
				if ($("#service").val() == "< ?php echo Consignment::SERVICE_INTERNATIONAL; ?>")
				{
					$("#form_action").val("pieces");
					$("#bookingForm").submit();
				}
			});*/
		});
		//-->
		function numbersonly(e){
			var unicode=e.charCode? e.charCode : e.keyCode
			if (unicode!=8)
			{
				if(unicode==46)
				{}
				else if (unicode<48||unicode>57) //if not a number
				return false //disable key press
			}
		}
		
		$(document).ready(function(){
			$("#number_pieces").change(function() 
			{
				if(document.bookingForm.add_dim.checked == true)
				{
					$("#someListToAlter").show();
					var currentpieces= document.bookingForm.hidden_number_pieces.value;
					//if(currentpieces == 0)
					//currentpieces = 1;
					$("someListToAlter").empty()
					var textualValue = $('#number_pieces').val();
					var number;
					number = parseInt(textualValue);
					var d=document.getElementById("someListToAlter");
					$('#someListToAlter').empty();
					 if (number>currentpieces)
					 {
					   for (var i=currentpieces; i<number; i++) 
					   {
						   d.innerHTML+="<div style='float:left;width:275px;'><strong>Weight:</strong> <input type='text' name='parcel_weight[]' class='form_field_coll1' style='width: 200px' onkeypress='return numbersonly(event)'>&nbsp;&nbsp;&nbsp;</div>";
						   d.innerHTML+="<div style='float:left;width:320px; '><strong>Length:</strong> <input type='text' name='parcel_length[]' class='form_field_coll1' style='width: 235px' onkeypress='return numbersonly(event)'>&nbsp;&nbsp;&nbsp;</div>" ;
						   d.innerHTML+="<div style='float:left;width:295px; '><strong>Width:</strong>  <input type='text' name='parcel_width[]' class='form_field_coll1' style='width: 240px'  onkeypress='return numbersonly(event)'>&nbsp;&nbsp;&nbsp;</div>" ;
						   d.innerHTML+="<div style='float:left;width:275px;'><strong>Height:</strong> <input type='text' name='parcel_height[]' class='form_field_coll1'  style='width: 205px'  onkeypress='return numbersonly(event)'>&nbsp;&nbsp;&nbsp;<br/><div>";
						  
						 //   d.innerHTML+="<div style='float:left;width:220px; '><strong>Commodity:</strong>  <input type='text' name='parcel_commodity[]' class='form_field_coll1' style='width: 180px' >&nbsp;&nbsp;&nbsp;<br/></div>" ;
						   d.innerHTML+="<div style='clear:both;'></div>" ;
					   }
					 }
				}
				else 
				{
						$("#someListToAlter").hide();
				}
				
				if(document.bookingForm.add_perform.checked == true)
				{
							
				
					
					$("#performalist").show();
					var currentpieces= document.bookingForm.hidden_number_pieces.value;
					//if(currentpieces == 0)
					//currentpieces = 1;
					$("performalist").empty()
					var textualValue = $('#number_pieces').val();
					var number;
					number = parseInt(textualValue);
					var d=document.getElementById("performalist");
					$('#performalist').empty();
					 if (number>currentpieces)
					 {
						 
						 d.innerHTML+="<div style='float:left;width:150px;'><strong>Terms of Payment:</strong> <input type='text' name='performa_payment' class='form_field_coll1' style='width: 130px'>&nbsp;&nbsp;&nbsp;</div>";						   
						 d.innerHTML+="<div style='float:left;width:150px;'><strong>Terms of Export:</strong> <select id='performa_export' name='performa_export' class='form_field_coll1'  style='width: 130px' ><option value='temporary'>Temporary</option> <option value='permanent'>Permanent</option> </select> &nbsp;&nbsp;&nbsp;</div>"	
//						 d.innerHTML+="<div style='float:left;width:230px;'><strong>Terms of Export:</strong> <input type='text' name='performa_export' class='form_field_coll1' style='width: 200px'>&nbsp;&nbsp;&nbsp;</div>";						   
						 d.innerHTML+="<div style='float:left;width:150px;'><strong>Comments:</strong> <input type='text' name='performa_comments' class='form_field_coll1' style='width: 130px'>&nbsp;&nbsp;&nbsp;</div>";						   
						 d.innerHTML+="<div style='float:left;width:150px;'><strong>Terms of Delivery:</strong> <input type='text' name='performa_invoice' class='form_field_coll1' style='width: 130px'>&nbsp;&nbsp;&nbsp;</div>";
						 d.innerHTML+="<div style='float:left;width:150px;'><strong>Payer of GST/VAT:</strong> <input type='text' name='performa_vat' class='form_field_coll1' style='width: 130px'>&nbsp;&nbsp;&nbsp;</div>";
						 d.innerHTML+="<div style='float:left;width:150px;'><strong>Harm.Comm.Code:</strong> <input type='text' name='performa_code' class='form_field_coll1' style='width: 130px'>&nbsp;&nbsp;&nbsp;</div>";	 
 						 d.innerHTML+="<div style='float:left;width:150px;'><strong>Add Sender Details:</strong><input class='sendDetail demo' type='button' value='Sender Details' id='sendDetail' name='sendDetail'  class='form_field_coll1'>&nbsp;&nbsp;&nbsp;</div>";						   
						  d.innerHTML+="<div style='clear:both;'></div>" ;
					   for (var i=currentpieces; i<number; i++) 
					   {
						  
						   d.innerHTML+="<div style='float:left;width:250px; padding-top:16px;padding-right:10px;'><B>Add Item Description</b><input class='osx demo' type='button' value='Add' id='"+ i +"' name='osx' style='width: 50px' class='form_field_coll1'>&nbsp;&nbsp;&nbsp;</div>";
						 //  d.innerHTML+="<div style='float:left;width:290px;'><strong>Tarrif No:</strong> <input type='text' name='performa_commodity[]' class='form_field_coll1' style='width: 235px'>&nbsp;&nbsp;&nbsp;</div>";						   
						 //  d.innerHTML+="<div style='float:left;width:255px;'><strong>Quantity:</strong> <input type='text' name='performa_quantity[]' class='form_field_coll1' style='width: 200px' onkeypress='return numbersonly(event)'>&nbsp;&nbsp;&nbsp;</div>";
						   //d.innerHTML+="<div style='float:left;width:140px;'><strong>Weight:</strong> <input type='text' name='performa_weight[]' class='form_field_coll1' style='width: 100px' onkeypress='return numbersonly(event)'>&nbsp;&nbsp;&nbsp;</div>";
					      // d.innerHTML+="<div style='float:left;width:295px;'><strong>Value:</strong> <input type='text' name='performa_value[]' class='form_field_coll1' style='width: 240px' onkeypress='return numbersonly(event)'>&nbsp;&nbsp;&nbsp;</div>";
						   	
						   //d.innerHTML+="<div style='float:left;width:275px;'><strong>Total Value:</strong> <input type='text' name='performa_tvalue[]' class='form_field_coll1' style='width: 205px' onkeypress='return numbersonly(event)'>&nbsp;&nbsp;&nbsp;</div>";

 						 //   d.innerHTML+=" <input type='text' name='hidden_item_desc[]' id='hidden_item_desc[]'  />";
					 	 //  d.innerHTML+="<input type='text' name='hidden_item_value[]' id='hidden_item_value[]'	 />";

						   //d.innerHTML+="<div style='float:left;width:340px;'><strong>Gross Weight:</strong> <input type='text' name='performa_gweight[]' class='form_field_coll1' style='width: 300px' onkeypress='return numbersonly(event)'>&nbsp;&nbsp;&nbsp;</div>";						   
						   d.innerHTML+="<div style='clear:both;'></div>" ;
					   }
					 //  alert($(".osx").val());
					   //$(".osx").live("click", OSX.init());
					   
					   OSX.init();
					  
					   	
					   	
					 }
				}
				else
				{
					$("#performalist").hide();
						
				}
				if($("#number_pieces").val()>0)
				{
					$("#weight-option").toggle();
				}
			});
		//	$("#number_pieces").change();
		$("#fullpallets").blur(function(){
			var	fulPal	=	0;
			var	halPal	=	0;
			var	qtrPal	=	0;
			if($("#fullpallets").val() != '')
				fulPal	= $("#fullpallets").val();
			if($("#halfpallets").val() != '')
				halPal	= $("#halfpallets").val();
			if($("#qtrpallets").val() != '')
				qtrPal	= $("#qtrpallets").val();
			
			$('#palletlifts').val(parseInt(fulPal)+parseInt(halPal)+parseInt(qtrPal));
			
			});
		$("#halfpallets").blur(function(){
			var	fulPal	=	0;
			var	halPal	=	0;
			var	qtrPal	=	0;
			if($("#fullpallets").val() != '')
				fulPal	= $("#fullpallets").val();
			if($("#halfpallets").val() != '')
				halPal	= $("#halfpallets").val();
			if($("#qtrpallets").val() != '')
				qtrPal	= $("#qtrpallets").val();
			
			$('#palletlifts').val(parseInt(fulPal)+parseInt(halPal)+parseInt(qtrPal));
			});
		$("#qtrpallets").blur(function(){
			var	fulPal	=	0;
			var	halPal	=	0;
			var	qtrPal	=	0;
			if($("#fullpallets").val() != '')
				fulPal	= $("#fullpallets").val();
			if($("#halfpallets").val() != '')
				halPal	= $("#halfpallets").val();
			if($("#qtrpallets").val() != '')
				qtrPal	= $("#qtrpallets").val();
			
			$('#palletlifts').val(parseInt(fulPal)+parseInt(halPal)+parseInt(qtrPal));
			});
		});
		
		
		
		function addperformafiled()
		{
			var currentdetail = document.bookingForm.hidden_item_details.value;
			var currentpieces= document.bookingForm.hidden_number_pieces.value;
			var textualValue = document.getElementById("number_pieces").value;
			if(currentdetail == "")
			{
				for (var i=currentpieces; i<textualValue; i++) 
				{		
						
					document.bookingForm.hidden_item_details.value += "#";
					//alert(document.bookingForm.hidden_item_details.value);
				}
			}
			else
			{
				var arrStr = currentdetail.split(/[#]/);
				var olditempieces = arrStr.length - 1;
				
				if(textualValue > olditempieces)
				{
					for (var i=olditempieces; i<textualValue; i++) 
					{	
						document.bookingForm.hidden_item_details.value += "#";
						//alert(document.bookingForm.hidden_item_details.value);
					}
				}
				else
				{
					var newitem = olditempieces - textualValue;
					document.bookingForm.hidden_item_details.value = "";
					
					someArray = arrStr.slice(0, textualValue); // first element removed
					
					for(arr in someArray)
					{
						document.bookingForm.hidden_item_details.value += someArray[arr] + "#";
					}
						
					//	alert(document.bookingForm.hidden_item_details.value);
					
				}
				
			}
		}

		function addSenderDetail()
		{
				var currentdetail= document.getElementById('hidden_sender_details').value;
				var sender_company = document.getElementById('billing_company').value ;
				var sender_contact = document.getElementById('billing_contact').value ;
				var add_line_1 = document.getElementById('add_line_1').value ;
				var add_line_2 = document.getElementById('add_line_2').value ;
				var add_line_3 = document.getElementById('add_line_3').value ;
				var sender_city = document.getElementById('billing_city').value ;
				var sender_country = document.getElementById('billing_country').value ;
				var sender_postcode = document.getElementById('billing_postcode').value ;
				var sender_telephone = document.getElementById('billing_telephone').value ;
				
				if(sender_company != "")
				{
					currentdetail = sender_company + "||";
				}
				else
				{
					currentdetail = "||";

				}
				
				if(sender_contact != "")
				{
					currentdetail += sender_contact + "||";
				}
				else
				{
					currentdetail += "||";

				}
				
				if(add_line_1 != "")
				{
					currentdetail += add_line_1 + "||";
				}
				else
				{
					currentdetail += "||";

				}
				if(add_line_2 != "")
				{
					currentdetail += add_line_2 + "||";
				}
				else
				{
					currentdetail += "||";

				}
				if(add_line_3 != "")
				{
					currentdetail += add_line_3 + "||";
				}
				else
				{
					currentdetail += "||";

				}
				if(sender_city != "")
				{
					currentdetail += sender_city + "||";
				}
				else
				{
					currentdetail += "||";

				}
				if(sender_country != "")
				{
					currentdetail += sender_country + "||";
				}
				else
				{
					currentdetail += "||";

				}
				if(sender_postcode != "")
				{
					currentdetail += sender_postcode + "||";
				}
				else
				{
					currentdetail += "||";

				}
				if(sender_telephone != "")
				{
					currentdetail += sender_telephone + "||";
				}
				else
				{
					currentdetail += "||";
				}
				document.getElementById('hidden_sender_details').value = currentdetail;
				
		}
		
		var arrdec = "";
		var arrvalue = "";
		var arrqty = "";
		var arrtarrif = "";
		var arrcountry = "";
		var arritem = new Array();
		var arritemdesc = new Array();
		
		function fillitemdesc()
		{
			arrdec = "";
			arrvalue = "";
			arrqty = "";
			arrtarrif = "";
			arrcountry = "";
			
			arritem[0] = null;
			arritem[1] = null;
			arritem[2] = null;
			
			
			
			var currentpieces= document.bookingForm.hidden_number_pieces.value;
			
			var textualValue = $('#itemdesciption').val();
			$('#osx-container').attr('style', 'height: auto;left: 250px;position: fixed;top: 0;width: 980px;z-index: 1002;');
			var number;
			number = parseInt(textualValue);
			
			var d=document.getElementById("itemlist");
			$('#itemlist').empty();
			arritem[0] = textualValue ;

			 if (number>currentpieces)
			 {
			   for (var i=currentpieces; i<number; i++) 
			   {
				  //          onblur= 'adddesc(this.value, \"dec\");' 
				  // onblur= 'adddesc(this.value, \"val\");'  onblur= 'adddesc(this.value, \"qty\");'      
				   d.innerHTML+="<tr><td> <input type='text' name='i_desc["+i+"]' id='i_desc["+i+"]' class='form_field_coll1' style='width: 150px; float:left; margin-right:30px;' >&nbsp;&nbsp;&nbsp;</td>";
				   d.innerHTML+="<td><input type='text' name='i_value["+i+"]' id='i_value["+i+"]' class='form_field_coll1' style='width: 150px; float:left; margin-right:30px;' >&nbsp;&nbsp;&nbsp;</td>";						   
				   d.innerHTML+="<td><input type='text' name='i_quantity["+i+"]' id='i_quantity["+i+"]' class='form_field_coll1' style='width: 150px; float:left; margin-right:30px; ' >&nbsp;&nbsp;&nbsp;</td>";						   				   
				   d.innerHTML+="<td><input type='text' name='i_tarrifno["+i+"]' id='i_tarrifno["+i+"]' class='form_field_coll1' style='width: 150px; float:left; margin-right:30px;' >&nbsp;&nbsp;&nbsp;</td>";						   
				   d.innerHTML+="<td><input type='text' name='i_country["+i+"]' id='i_country["+i+"]' class='form_field_coll1' style='width: 150px; float:left; margin-right:30px; margin-top:-25px;' >&nbsp;&nbsp;&nbsp;<div style='clear:both;'></div> </td></tr>";						   
				 
			   }
			 }
		}
		
		function addDetail()
		{
			
			var currentdetail= document.bookingForm.hidden_item_details.value;
			var addformid = document.getElementById("addformid").value;
			var arrStr = currentdetail.split(/[#]/);
			
			var arrlength = arrStr.length - 1;
			
			var numberitem = document.getElementById("itemdesciption").value;
			
			arritem[0] = numberitem;
			for (var i=0; i<numberitem; i++)
			{
				
				if(arrdec == "")
				{
					arrdec = document.getElementById('i_desc[' + i + ']').value ;				 
				}
				else
				{
					arrdec += "||" + document.getElementById('i_desc[' + i + ']').value;			
				}
				if(arrvalue == "")
				{
					arrvalue = document.getElementById('i_value[' + i + ']').value ;				 
				}
				else
				{
					arrvalue += "||" + document.getElementById('i_value[' + i + ']').value;		
				}
				if(arrqty == "")
				{
					arrqty = document.getElementById('i_quantity[' + i + ']').value ;		 
				}
				else
				{
					arrqty += "||" + document.getElementById('i_quantity[' + i + ']').value;		
				}
				if(arrtarrif == "")
				{
					arrtarrif = document.getElementById('i_tarrifno[' + i + ']').value ;			 
				}
				else
				{
					arrtarrif += "||" + document.getElementById('i_tarrifno[' + i + ']').value;	
				}
				if(arrcountry == "")
				{
					arrcountry = document.getElementById('i_country[' + i + ']').value ;
				}
				else
				{
					arrcountry += "||" + document.getElementById('i_country[' + i + ']').value;				
				}
				
			}
				arritem[1] = [""+ arrdec + ""];
				arritem[2] = [""+ arrvalue + ""];
				arritem[3] = [""+ arrqty + ""];
				arritem[4] = [""+ arrtarrif + ""]
				arritem[5] = [""+ arrcountry + ""]
				
				if(arritem.length >0)
				{
					arrStr[addformid] = arritem;
					document.bookingForm.hidden_item_details.value = arrStr.join("#");
				}
			
			//alert(document.bookingForm.hidden_item_details.value);
			
		}
		
		function edititemdesc()
		{
			var arrDes = [];
			var arrVal = [];
			var arrQty = [];
			var arrTar = [];
			var arrCoun = [];
			arrdec = "";
			arrvalue = "";
			arrqty = "";
			arrtarrif = "";
			arrcountry = "";
			arritem[0] = null;
			arritem[1] = null;
			arritem[2] = null;		
			var currentdetail= document.bookingForm.hidden_item_details.value;
			var addformid = document.getElementById("addformid").value;
			//alert(currentdetail);
			//spliting whole string by #
			var arrStr = currentdetail.split(/[#]/);
			//alert(arrStr);
			var arrdetail = arrStr[addformid] ;
			//alert(arrdetail);
			if(arrdetail.length > 0)
			{
				//after split taking particular record and split it by ,
				var arrinfo = arrdetail.split(/[,]/);
				//alert(arrinfo);
				document.getElementById("itemdesciption").value = arrinfo[0];
			//	fillitemdesc();
				var description = arrinfo[1];
				//alert(description);
				arrDes = description.split('||');
				var Value = arrinfo[2];
				arrVal = Value.split('||');
				var Quantity = arrinfo[3];
				arrQty = Quantity.split('||');
				var Tarriff = arrinfo[4];
				arrTar = Tarriff.split('||');
				var Country = arrinfo[5];
				arrCoun = Country.split('||');
				
				//alert(arrinfo[0]);
				var d=document.getElementById("itemlist");
				$('#itemlist').empty();
				
				for (var i=0; i<arrinfo[0]; i++)
				{
					
					 d.innerHTML+="<tr><td> <input type='text' value='"+ arrDes[i] +"' name='i_desc["+i+"]' id='i_desc["+i+"]' class='form_field_coll1' style='width: 150px; float:left; margin-right:30px;' >&nbsp;&nbsp;&nbsp;</td>";
				  	 d.innerHTML+="<td><input type='text' value='"+ arrVal[i] +"' name='i_value["+i+"]' id='i_value["+i+"]' class='form_field_coll1' style='width: 150px; float:left; margin-right:30px;' >&nbsp;&nbsp;&nbsp;";						   
				  	 d.innerHTML+="<td><input type='text' value='"+ arrQty[i] +"' name='i_quantity["+i+"]' id='i_quantity["+i+"]' class='form_field_coll1' style='width: 150px; float:left; margin-right:30px; ' >&nbsp;&nbsp;&nbsp;";
					 d.innerHTML+="<td><input type='text' value='"+ arrTar[i] +"' name='i_tarrifno["+i+"]' id='i_tarrifno["+i+"]' class='form_field_coll1' style='width: 150px; float:left; margin-right:30px;' >&nbsp;&nbsp;&nbsp;";						   
					 d.innerHTML+="<td><input type='text' value='"+ arrCoun[i] +"' name='i_country["+i+"]' id='i_country["+i+"]' class='form_field_coll1' style='width: 150px; float:left; margin-right:30px; margin-top:-25px;' >&nbsp;&nbsp;&nbsp<div style='clear:both;'></div> </td></tr>";						   				  						   
				 
//					document.getElementById('i_desc')[i].value = arrDes[i];
				}
				
			}
			
		}
		
		
		function validateEmail()
		{
			sEmail = $('#email').val();
			var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			if (filter.test(sEmail))
				return true;
			else if(sEmail != '')
			{
				
				alert( 'Please enter valid email address');
				$("#email").focus();
				return false;
			}
		}