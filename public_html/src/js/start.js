/*
	Browser Detect set v1.0
	Detects the browser and its version (IE6+, Opera, Firefox, Chrome, Safari )

	http://xiper.net/

	Author Andrei Kosyack

    Выпрямляем непонятные косяки браузеров путем отлова



function browserDetectNav(chrAfterPoint)
{
var
	UA=window.navigator.userAgent,
	OperaB = /Opera[ \/]+\w+\.\w+/i,
	OperaV = /Version[ \/]+\w+\.\w+/i,
	FirefoxB = /Firefox\/\w+\.\w+/i,
	ChromeB = /Chrome\/\w+\.\w+/i,
	SafariB = /Version\/\w+\.\w+/i,
	IEB = /MSIE *\d+\.\w+/i,
	SafariV = /Safari\/\w+\.\w+/i,
	browser = new Array(),
	browserSplit = /[ \/\.]/i,
	OperaV = UA.match(OperaV),
	Firefox = UA.match(FirefoxB),
	Chrome = UA.match(ChromeB),
	Safari = UA.match(SafariB),
	SafariV = UA.match(SafariV),
	IE = UA.match(IEB),
	Opera = UA.match(OperaB);

		if ((!Opera=="")&(!OperaV=="")) browser[0]=OperaV[0].replace(/Version/, "Opera")
				else
					if (!Opera=="")	browser[0]=Opera[0]
						else
							if (!IE=="") browser[0] = IE[0]
								else
									if (!Firefox=="") browser[0]=Firefox[0]
										else
											if (!Chrome=="") browser[0] = Chrome[0]
												else
													if ((!Safari=="")&&(!SafariV=="")) browser[0] = Safari[0].replace("Version", "Safari");

	var outputData;

	if (browser[0] != null) outputData = browser[0].split(browserSplit);
	if (((chrAfterPoint == null)|(chrAfterPoint == 0))&(outputData != null))
		{
			chrAfterPoint=outputData[2].length;
			outputData[2] = outputData[2].substring(0, chrAfterPoint);
			return(outputData);
		}
			else
				if (chrAfterPoint != null)
				{
					outputData[2] = outputData[2].substr(0, chrAfterPoint);
					return(outputData);
				}
					else	return(false);
}

function browserDetectJS() {
var
	browser = new Array();

	if (window.opera) {
		browser[0] = "Opera";
		browser[1] = window.opera.version();
	}
		else
		if (window.chrome) {
			browser[0] = "Chrome";
		}
			else
			if (window.sidebar) {
				browser[0] = "Firefox";
			}
				else
					if ((!window.external)&&(browser[0]!=="Opera")) {
						browser[0] = "Safari";
					}
						else
						if (window.ActiveXObject) {
							browser[0] = "MSIE";
							if (window.navigator.userProfile) browser[1] = "6"
								else
									if (window.Storage) browser[1] = "8"
										else
											if ((!window.Storage)&&(!window.navigator.userProfile)) browser[1] = "7"
												else browser[1] = "Unknown";
						}

	if (!browser) return(false)
		else return(browser);
}

function getBrowser(chrAfterPoint) {
	var
		browserNav = browserDetectNav(chrAfterPoint),
		browserJS = browserDetectJS();

	if (browserNav[0] == browserJS[0]) return(browserNav)
		else
			if (browserNav[0] != browserJS[0]) return(browserJS)
				else
					return(false);
}


function isItBrowser(browserCom, browserVer, detectMethod) {
var browser;

switch (detectMethod) {
	case 1: browser = browserDetectNav(); break;
	case 2: browser = browserDetectJS(); break;
	default: browser = getBrowser();
};

	if ((browserCom == browser[0])&(browserVer == browser[1])) return(true)
		else
			if ((browserCom == browser[0])&((browserVer == null)||(browserVer == 0))) return(true)
				else return(false);
};



function bdetect()
{
   if(isItBrowser("Firefox"))
   {
        $(".rgh_dat").css("height","108px");
   }
}
window.onload = bdetect;
*/

var www = document.body.clientWidth;


(function($){
			$(window).load(function(){


				/* demo functions */
			      //прячем и открываем по клику 2футер
   $(".footer2").hide();


  $("body").on( "click", ".wrap .footer .right a", function(showdf) {
            showdf.preventDefault();

        $(".wrap .footer2").slideDown( 400 );


         var open = function(){
                 $(document.body).trigger("tick");
                   google.maps.event.trigger(mpp,'resize');
               	$.scrollTo( $('.wrap #mpp'), {duration:400} );
                $(".wrap .footer").hide();
          };
     setTimeout(open, 400);
    });




    $("body").on( "click", ".map_find .footer .right a", function(showdff) {
            showdff.preventDefault();
        $(".map_find .footer2").slideDown( 400 );

         var open22 = function(){
                 $(document.body).trigger("tick");
                 $mmap = $(".map_find #mpp");
                   google.maps.event.trigger($mmap[0],'resize');
               	$.scrollTo( $('.map_find #mpp'), {duration:400} );
                $(".map_find .footer").hide();
          };
     setTimeout(open22, 900);
    });


	});
		})($);



 //скрываем фильтр для моб версии  по клику
 $( document ).ready(function() {
  $( ".show_filt, .hider_f" ).click(function() {
     $(".filter .down, #countries_msdd").fadeToggle();
});

  //открываем форму входа по LOGIN кнопке
  $("#login").click(function(evv) {
     evv.preventDefault();
     $(".hid").fadeToggle();
     $(".reg-block .registration-link a.zabil").css( "display", "block");
     $(".reg-block .registration-link.rega.pad").toggleClass("pad");
     $("#login a").addClass( "act" );
    // $(".registration-link.rega").toggleClass( "sds");

});

$("#avtor").click(function(bbb) {
     bbb.preventDefault();
     $(".reg-block.w2").hide();
     $(".reg-block.w1").show();

    // $(".registration-link.rega").toggleClass( "sds");

});


 //открываем форму забыли пароль
  $("#zabil").click(function(evvbb) {
     evvbb.preventDefault();
     $(".reg-block.w2").fadeToggle();
     $(".w1").hide();

    // $(".registration-link.rega").toggleClass( "sds");

});

//фикс стиля селекта
$( "body" ).on( "mouseover", ".ddArrow", function() {
  $(this).next().addClass("over");
}).on( "mouseout", ".ddArrow", function() {
  $(this).next().removeClass("over");
}) ;



 //  $( ".nav2 >  li:first  li " ).mouseover(function() {
// $(this).addClass("hover");

//}).mouseout(function(){
//  $(this).removeClass("hover");
//});




});

 //скрываем фильтр для моб версии при ресайзе
$(window).bind('resize orientationchange', function() {
	www = document.body.clientWidth;
	adjustMenu2();
});


var adjustMenu2 = function() {
	if (www < 768) {
         if ( $("#mpp").length  > 0 ) {
             //alert("карта!");
             google.maps.event.trigger(mpp,'resize');
         }


 $(function () {
    $(window).scroll(function ( evenscroll) {

        if ($(this).scrollTop() > 300) {

           //  $(".filter .down, #countries_msdd").hide();     //скрываем фильтр для моб версии если скролл 200
    } else {

        }
    })
});
    $(".nav2 ul.f15").removeClass("f15")
    //$(".nav2 ul.f16").removeClass("f16")
	}

 	else if (www >= 768) {
	   $(".filter .down, #countries_msdd").css("display","block");
	   $(".filter .down, #countries_msdd").show();
       $("#filt, .header").trigger("sticky_kit:detach");

          $(function () {
    $(window).scroll(function ( evenscroll2) {

        if ($(this).scrollTop() > 301) {

            // $(".filter .down, #countries_msdd").show();
                //скрываем фильтр для моб версии если скролл 200
    } else {

        }
    })
});


	} ;


};



//если в гл больше 15 li то другой вид даем
 $('.nav2 ul ul').each(function() {
        if ($(this).find('li').length >= 15) {
            $(this).addClass("f15");
    }
});



    // $(".registration-link.rega").toggleClass( "sds");



 $('.nav2 li:first-child').next().children("ul").each(function() {
        if ($(this).find('li').length >= 16) {
            $(this).addClass("f16");
    }
});


 $('.nav2 li:first-child').next().next().children("ul").each(function() {
        if ($(this).find('li a').length >= 16) {
            $(this).addClass("f16");
    }
});



 $('.nav2 ul ul').each(function() {
        if ($(this).find('li').length >= 4) {
            $(this).addClass("f15mob");
    }
});



$(document).ready(function() {

       $(".f15mob").siblings("a.parent").off('click').on( "click", function(evvg) {
evvg.preventDefault();
      $(this).addClass("dddf");

    if (!$(this).parent().hasClass("hover"))
      $(this).parent().addClass("hover");
 else  $(this).parent().removeClass("hover");
    $("#filt, .header").trigger("sticky_kit:detach");


});

  //клонируем , добавляем +1 к номеру, меняем id for и вставдяем еще кнопку удаления, меняем высоту карты
 $("body").on( "click", ".kont_clon", function(clon) {
    clon.preventDefault();
    var countPlayers = $('.heigh .imt_kont').length;
    $(this).parents(".imt_kont").clone().appendTo(".heigh").find(".nom").text('№'+(countPlayers+1)).next().find("input").each(function()
        {$(this).attr("id",$(this).attr("id")+"1");
         $(this).next().attr("for",$(this).next().attr("for")+"1");

         //.find(".yazik spa").remove()

    }).parents(".imt_kont").find(".logo_prof").append('<span class="delet">Удалить</span>')
    .parents(".imt_kont")
    .find("fieldset .mobil .right input, .orig, .forma label.plus.la_lang .wrp + input, input[type='text']").each(function(){
     $(this).attr("id",$(this).attr("id")+"1")
    }).parents(".imt_kont").find("fieldset .mobil .right label").each(function(){
     $(this).attr("for",$(this).attr("for")+"1")
     })
     .parents(".imt_kont").find(".yazik").next().find("input[type='checkbox']").each(function(){
     $(this).prop("checked", false);
     }).parents(".imt_kont").find(".yazik span").remove();


    //изменяем высоту карты
     var he =  $("#map_pro").height();
    $("#map_pro").height(he+270);
    google.maps.event.trigger(map_pro,'resize');

});

//открываем
 $("body").on( "click", ".where .index .des", function(show) {
    $(this).find(".downbox").show();
});

 $("body").on( "click", ".fotos .car_f ", function(checked1) {
    $(this).find("input").prop("checked", true);
});


 $("body").on( "click", ".fotos i ", function(remo2) {
    $(this).parent().remove();
});

 //Удаляем профиль
$("body").on( "click", ".logo_prof .delet", function(remov) {
     remov.preventDefault();
    $(this).parents(".imt_kont").remove();
      var hei =  $("#map_pro").height();
    $("#map_pro").height(hei-270);
    google.maps.event.trigger(map_pro,'resize');
});


 function ActivatePhotos() {
        
        $(".content .catalog .item .images span").on("click", function (e) {

            e.preventDefault();
            var currentImgUrl = $(this).parent().find("IMG.real").attr("src");
            var photoString = $(this).parent().data("photos");
            var offsetDesired = $(this).hasClass("prev") ? -1 : 1;
            var currentPhotoId = GetImageIdFromUrl(currentImgUrl);
            var newId = GetImageIdInList(currentPhotoId, photoString, offsetDesired);
            var newImgUrl = SetImageIdToUrl(newId, currentImgUrl);

            $(this).parent().find("IMG.real").attr("src", newImgUrl);

        });
        
        function GetImageIdFromUrl(photoUrl) {
            var id = photoUrl;
            id = id.substring(id.lastIndexOf("/") + 1);
            id = id.substring(0, id.indexOf("."));
            return id;
        };

        function SetImageIdToUrl(id, photoUrl) {
            var newUrl = photoUrl;
            newUrl = newUrl.substring(0, newUrl.lastIndexOf("/") + 1);
            newUrl += id;
            newUrl += ".jpg";
            return newUrl;
        };

        function GetImageIdInList(currentId, photoString, offset) {

            var photos = photoString.split(",");
            var currentIndex = 0;
            for (var i = 0; i < photoString.length; i++) {
                if (photos[i] == currentId) {
                    currentIndex = i;
                    break;
                }
            }

            currentIndex += offset;
            if (currentIndex < 0) currentIndex = photos.length - 1;
            if (currentIndex > photos.length - 1) currentIndex = 0;
            return photos[currentIndex];

        };
        
    };

ActivatePhotos();
 //по клику вставляем текст

   $("body").on( "click", "#germ", function(idx1) {
         $(this).parents(".des").find("#min_ind").text("de");
    });
   $("body").on( "click", "#belg", function(idx2) {
        $(this).parents(".des").find("#min_ind").text("be");
    });
   $("body").on( "click", "#fran", function(idx3) {
        $(this).parents(".des").find("#min_ind").text("fr");
    });
     $("body").on( "click", "#nider", function(idx4) {
        $(this).parents(".des").find("#min_ind").text("nd");
    });
     $("body").on( "click", "#itals", function(idx5) {
        $(this).parents(".des").find("#min_ind").text("it");
    });
    $("body").on( "click", "#rossi", function(idx6) {
        $(this).parents(".des").find("#min_ind").text("rs");
    });


  $("body").on( "click", "#rossi", function(idx6) {
        $(this).parents(".des").find("#min_ind").text("rs");
    });



   $("#price").keypress(function(onlycifr){
    if (onlycifr.which < 48 || onlycifr.which > 57  ) return false;
});
  $("body").on( "click", ".nav2.minis li li a", function(calcproc) {
        var  prc = $("#price").val();
        //var prc = parseInt($("#price").value ,10);
          if ( prc < 0) {
             alert('Значение цены должно быть больше 0');
             $("#price").val(0);
           }

  });




  //по умолявнию на 18%
  $("body").on( "click", "#clik_calc_f", function(calcprocnow) {
      var  valco = $("#price").val();

      if (valco > 0) {
        var nn = parseInt(valco);
        var newval = nn-(nn*18/100);
        fino = newval.toFixed(2);
        $("#brutto").one().text(fino);
      }
     else {

     }
  });

    $("body").on( "click", "#cc16,#cc17,#cc18,#cc19", function(calcproc) {
      var  valc = $("#price").val();
      var newva = valc-(valc*parseInt($(this).attr('id').replace('cc', ''))/100);
      fin = newva.toFixed(2);
      $("#brutto").text(fin);
  });


 var recalko = function() {

   if ($(".nav2.minis").not(':hidden')) {
     var proc =  $(".nav2.minis > li > .parent").text().replace('%', '');
     var  valcon = $("#price").val();
      if (valcon > 0) {
        var mm = parseInt(valcon);
        var newvaln = mm-(mm*proc/100);
        finos = newvaln.toFixed(2);
        $("#brutto").text(finos);
      }

   }

}


$("#price").keyup(function () {
    recalko();
}).keyup();



 // #price ценна нетто
 // #kmb пробег км
 // #vkub обчем движка


 //,  #sky, #BlackBerry, #emailm  добавляем поля по клику и меняем id
$("body").on( "click", "#teleph", function(ados) {
    ados.preventDefault();
    $(this).parents(".imt_kont").find("fieldset").append('<label class="teleph">Телефон<b></b><input type="text" value="" name="tel2" id="tel2"></label>').find("#tel2").attr("id",$(this).attr("id")+Math.floor((Math.random() * 10000) + 1));
});


$("body").on( "click", "#mobil", function(ados2) {
    ados2.preventDefault();
    $(this).parents(".imt_kont").find("fieldset").append('<label class="mobil">Мобильный<b></b><input type="text" value="" name="mob2" id="mobb2"></label>').find("#mobb2").attr("id",$(this).attr("id")+Math.floor((Math.random() * 10000) + 1));
});

$("body").on( "click", "#sky", function(ados3) {
    ados3.preventDefault();
    $(this).parents(".imt_kont").find("fieldset").append('<label class="sky">Skype<b></b><input type="text" value="" name="sk2" id="skype2"></label>').find("#skype2").attr("id",$(this).attr("id")+Math.floor((Math.random() * 10000) + 1));
});

$("body").on( "click", "#BlackBerry", function(ados4) {
    ados4.preventDefault();
    $(this).parents(".imt_kont").find("fieldset").append('<label class="BlackBerry">BlackBerry pin<b></b><input type="text" value="" name="bb2" id="blb2"></label>').find("#blb2").attr("id",$(this).attr("id")+Math.floor((Math.random() * 10000) + 1));
});

$("body").on( "click", "#emailm", function(ados5) {
    ados5.preventDefault();
    $(this).parents(".imt_kont").find("fieldset").append('<label class="emailm">Email<b></b><input type="text" value="" name="em22" id="emm2"></label>').find("#emm2").attr("id",$(this).attr("id")+Math.floor((Math.random() * 10000) + 1));
});

$("body").on( "click", "#faceb", function(ados6) {
    ados6.preventDefault();
    $(this).parents(".plus").prev().append('<label class="faceb">Facebook<b></b><input type="text" value="" name="em24" id="fb5"></label>').find("#fb5").attr("id",$(this).attr("id")+Math.floor((Math.random() * 10000) + 1));
});

$("body").on( "click", "#twit", function(ados7) {
    ados6.preventDefault();
    $(this).parents(".plus").prev().append('<label class="emailm">Twitter<b></b><input type="text" value="" name="tw24" id="twit2"></label>').find("#twit2").attr("id",$(this).attr("id")+Math.floor((Math.random() * 10000) + 1));
});
$("body").on( "click", "#weba ", function(ados8) {
    ados8.preventDefault();
    $(this).parents(".plus").prev().append('<label class="emailm">Вебсайт<b></b><input type="text" value="" name="web5" id="webd"></label>').find("#webd").attr("id",$(this).attr("id")+Math.floor((Math.random() * 10000) + 1));
});
$("body").on( "click", "#twit2", function(ados9) {
    ados9.preventDefault();
    $(this).parents(".plus").prev().append('<label class="twit2">Twitter<b></b> <input type="text" value="" name="twt5" id="wtw"></label>').find("#wtw").attr("id",$(this).attr("id")+Math.floor((Math.random() * 10000) + 1));
});
 $("body").on( "click", "#weba2", function(ados10) {
    ados10.preventDefault();
    $(this).parents(".plus").prev().append('<label class="weba2">Вебсайт<b></b><input type="text" value="" name="wb5" id="wwb5"></label>').find("#wwb5").attr("id",$(this).attr("id")+Math.floor((Math.random() * 10000) + 1));
});



   //открываем меню с чекбоксами
 $("body").on( "click", ".forma fieldset label b ", function(dele) {

    $(this).parents("label").detach();
});


 //открываем меню с чекбоксами
 $("body").on( "click", ".forma .plus a.langu", function(evv4) {
    evv4.preventDefault();
    $(this).parents(".wrp").find(".downbox").show();
});



//открываем меню удаления
 $("body").on( "click", ".content .catalog.predlo .item .edits .delet", function(del) {

    $(this).parent().find(".dele").addClass("open");
});
//удаляем большого блока машины с инфой
 $("body").on( "click", ".windos.dele .udal", function(del2) {

  $(this).parents(".item").fadeOut(500).addClass("dell");

    //setTimeout(seca, 500) // использовать функцию
    setTimeout(function() { $(".dell").detach();  }, 501);

});


//открываем меню с чекбоксами
 $("body").on( "click", ".content .catalog.predlo .item .edits .pause", function(pause) {

    $(this).parent().find(".paus").addClass("open");
});




;



var adjustLeft = function() {
var pos = $(".f16").position()
 var pos = $(".content .catalog .item .images").width();
  +pos
  parseInt(pos,10);

  if (www < 768) {

  var posi = $(".content .catalog.predlo .item .edits .windos").width();


    var righ = $(" .main > .container").width(); //ширина
    var righ2 = (righ*2/100)+11;   //2% от ширины +6px
    var righ3 = righ2-righ2*2+"px";   //отрицат.


    var righ22 = (righ*2/100)+20; //2% от .container
    var marg = righ22-righ22*2+"px" ; //отри

   //$(".f16").css({'left' :'-20px', 'right' : '-20px' });
  }
  else {


   var marg = pos+(pos*10/100)+20 ;
    var nmarg = marg-marg*2+"px";
    var righ = $(" .main > .container").width(); //ширина
    var righ2 = (righ*2/100)+11;   //2% от ширины +6px
    var righ3 = righ2-righ2*2+"px";   //отрицат.
   $(".f16").css({'left' : nmarg, 'right' : righ3 });

      var marg = pos+40;
    var nmarg = marg-marg*2+"px";
   $(".f16").css({'left' : nmarg });

  }

};

$( ".content .catalog.predlo .item .edits .rekls" ).click(function() {
  adjustLeft();
});

$(window).resize(function(){
    adjustLeft();
});



//открываем большого блока машины с инфой
 $("body").on( "click", ".content .catalog.predlo .item .edits .windos .clos", function(del2) {

    $(this).parents(".windos").removeClass("open")
});

//открываем большого блока машины с инфой
 $("body").on( "click", ".content .catalog.predlo .item .edits .rekls", function(del3) {

    $(this).parent().find(".rekla").addClass("open");
});



 //удаляем большого блока машины с инфой
 $("body").on( "click", ".content .catalog.predlo .item .edits .windos.paus .udal", function(del4) {

  alert("Это объявление больше не будет показваться!");



});


  $("#ssil").click(function(evv5) {
    evv5.preventDefault();

    $(this).find(".downbox").slideDown(400);

});


 //https://jsfiddle.net/tb36gwj5/114/
 // на сттранице профиля выбор языков
$("body").on( "click", "#lang_spis .checkbox", function(for_lang_profil_page) {
     var thisp = $(this).parents(".downbox").prev(); // .yazik
 					var ids =  $(this).find('input[type="checkbox"]').attr('data-lang');
          var inputCheck = $(this).find('input[type="checkbox"]').prop("checked");
          if (inputCheck == false) {
            thisp.find("span:contains("+ids+")").remove();
          } else if(inputCheck == true){
           thisp.find('b').after("<span>"+ids+", </span>");
          };
          if (   thisp.find('span').length > 0   ) {
             var last = thisp.find('span').last();
						 var srt = last.text().replace(/,/,''); //получить текст
						 last.text(srt);
          }
        });



 $(document).mouseup(function (e){ // событие клика по веб-документу
		var div = $(".downbox"); // тут указываем ID элемента
        var div2 = $(".addpole li");
		if (!div.is(e.target) // если клик был не по нашему блоку
 && div.has(e.target).length === 0 /*|| div2.is(e.target)*/ ) { // и не по его дочерним элементам
			div.hide(); // скрываем его
		}

	});




    $("body").on( "click", "#razv", function(razva) {

            $(".fix_map .map_find .container:first").slideToggle();


            //google.maps.trigger(all_car_map, 'latlng', '49.60, 8.9808');

        });
       //http://gmaps-samples-v3.googlecode.com/svn/trunk/spreadsheets/mapwithsidebar.html

   //открываем мега карту на весь экран
     $("body").on( "click", ".map-btn.main, .map-btn.mobile, #onmap", function(hiders1) {
        hiders1.preventDefault();

            $( ".fix_map" ).toggleClass("hid");
            $(".wrap .footer, .wrap .footer2").clone().appendTo(".map_find");
            $(".wrap .footer, .wrap .footer2").remove() ;
             google.maps.event.trigger(mpp,'resize');
            // google.maps.event.trigger(all_car_map,'resize');
             google.maps.event.trigger(all_car_map, 'resize', function(){
                all_car_map.setCenter( marker.getPosition() );
             });

});

//закрыть большую карту
    $("body").on( "click", "#closer_map, #mapcloser", function(hiders) {
             $( ".fix_map" ).toggleClass("hid");

            $(".fix_map .footer, .fix_map .footer2").clone().appendTo(".wrap");
            $(".fix_map .footer, .fix_map .footer2").remove() ;

        });




    });   // $(document).ready(function()




 //функциядобавления в  ТЕГ фильтра
 
    
	
     $('.nav2 li li:not(:has(ul))').children().addClass("clearsi");
         $('.nav2 >li > a').addClass("rod"); //якорь для копированя выбора в главного родителя

  $('.nav2 > li > a.rod').each(function(){
         var txt = $(this).text();
          $(this).after("<span class='hd'>"+txt+"</span>"); //копируем заголовки меню выбора
          var datas = $(this).after().text(); //
          $(this).next().next().find(" li li li a, .clearsi").attr('data-cmd', datas);
        });


    	$(".nav2 li li li a, .clearsi").click(function(esp) {
    	  	esp.preventDefault();
  		    // $(".nav2 li").removeClass('hover');
           var text = $(this).text();
            if ( $("#taags ul li:contains("+text+")").length == 1   ) {


           } else {

                               var _this = this;
                    $("#taags ul li a").each(function() {
                        if ($(this).data('cmd') == $(_this).data('cmd')) { 
                            $(this).parent().remove();
                    return false; 
        } 
    });

                $(this).parents(".hover").find(".rod").empty().html(text); 
                var cmd2 = $(this).attr('data-cmd');
		        $("#taags ul").append("<li><a data-cmd="+cmd2+" class='close' href='#'></a>"+text+"</li>");

            }
	    });


 //шаманим чтоб показать доп. контент



 $(document).ready(function() {

    $(".side_rec .car ").append('<div class="shad"></div>');
   //$(".side_rec .car ").append('<div class="shad2"></div>');


   var heig = [];

        $(".side_rec .car .bot").each(function(indx, element){
            heig.push($(element).outerHeight());
        });
       // $(".tags ul").text(heig);


        $(".side_rec .car .shad").each(function(index, value){
            var ind = index;
            $(this).css('bottom', function(index2, value2){
                var newSize = ((parseInt(value2.replace('px',''))-heig[ind])+10);
            return newSize;
        });
        });

  });









     //слайдеры чисел и фильтры
    $("body").on( "touchstart click", ".year-slider .jslider-pointer", function(filt) {

        if ($("#taags ul .sush").length){
      function sec() {
            var ttxt =  $("#Slider3" ).val();
            $("#sfilt").text(ttxt);

        };
      setInterval(sec, 1000) // использовать функцию
        }
    else {

        $("#taags ul").append("<li  class='sush'><a class='close' href='#'></a>  <span id='sfilt'></span></li>");

          function sec() {
            var ttxt =  $("#Slider3" ).val();
            $("#sfilt").text(ttxt);

        };
      setInterval(sec, 800) // использовать функцию
        }

    });



    //слайдеры чисел и фильтры
    $("body").on( "touchstart click", ".price-slider .jslider-pointer", function(filt2) {

        if ($("#taags ul .sush2").length){
      function sec2() {
            var ttxt2 =  $("#Slider2" ).val();
            $("#sfilt2").text(ttxt2);

        };
      setInterval(sec2, 1000) // использовать функцию
        }
    else {

        $("#taags ul").append("<li class='sush2'><a class='close' href='#'></a>  <span id='sfilt2'></span>&nbsp;€</li>");

          function sec2() {
            var ttxt2 =  $("#Slider2" ).val();
            $("#sfilt2").text(ttxt2);

        };
      setInterval(sec2, 800) // использовать функцию
        }

    });



  //удаляем филтр

  $("body").on( "click", "#taags ul .close", function(hr) {
            hr.preventDefault();
		var coptext = $(this).parent("li").text();
        	var txt2 = $(".nav2 a.rod:contains("+coptext+")").next().text();


    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	 $(".nav2 a.rod:contains("+coptext+")").text(txt2);

     //().removeClass("click");
      //по клику по удаления фильтра, ищем это в меню, копируем текс и ставим дефолт

    $(this).parent().detach();
       $(".nav2 a.clearsi:contains("+coptext+")").on('click');
        });




 $( ".clearsi" ).on( "click", function(hrbn) {
    hrbn.preventDefault();
    $(this).parents(".hover").removeClass("hover");
})



//закрываем меню
$( ".f16 a" ).on( "click", function(hre) {
    hre.preventDefault();
    $(this).parent().parent().parent().removeClass("hover");
})

//закрываем меню
$( ".f15 a" ).on( "click", function(hre) {
    hre.preventDefault();
    $(this).parent().parent().parent().removeClass("hover");
}) ;






  $( ".parent, .dddf" ).on( "click", function(nonclos) {
    nonclos.preventDefault();

});










if (www < 768) {
   //$("#filt").stick_in_parent({ offset_top: 95, recalc_every: 1 });
// $(".header").stick_in_parent( { offset_top: 0, recalc_every: 1 } );

   $(".nav2 ul.f15").removeClass("f15");
   //$(".nav2 ul.f16").removeClass("f16");
      // $( ".nav2 >  li:first  li " ).unbind( "mouseover" );
       // $( ".nav2 >  li:first  li " ).unbind( "mouseout" );

    $(function () {
    $(window).scroll(function ( ert) {

        if ($(this).scrollTop() > 300) {

            // $(".filter .down, #countries_msdd").hide();     //скрываем фильтр для моб версии если скролл 200
    } else {

        }
    })
});





 }

else  if (www >= 768) {



  $("#filt, .header").trigger("sticky_kit:detach");
}
