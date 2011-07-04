/**
 * Installer
 */
$(document).ready(function() {
  var path = $(location).attr('pathname');
  var step = path.substr(1).split("/")[2];
  $("#submitButton").css("width","30%");
  $("#submitButton").css("text-align","right");
  $("#submitButton").css("margin-left","auto");
  $("#submitButton").css("margin-right","auto"); 
  switch(step){
    case '1':
      step1();
      break;
    case '2':
      step2();
      break;
    case '3':
      step3();
      break;
    case '4':
      step4();
      break;
    case '5':
      step5();
      break;
  }
});

function step1(){
  var openInfo = null;
  var visibleField = 0;
  var fields = $(".settings").length;
  var animate=false;
  $("#goAdvanced").attr("checked",false);
  $("#advancedHolder").css("display","block");
  $(".settings").css("margin-left","auto");
  $(".settings").css("margin-right","auto");
  $(".settings").css("float","none");
  $(".settings:gt(0)").css("display","none");
  $(".settings div.holder").append("<p style='padding-left: 5px; padding-right: 5px'><span class='prev'>[prev]</span><span class='next'>[next]</span></p>");
  $(".settings div.holder p span").css("display","none");
  $(".settings div.holder p span").css("cursor","pointer");
  $(".settings:first div.holder p span.prev").css("visibility","hidden");
  $(".settings:last div.holder p span.next").css("visibility","hidden");
  $("#goAdvanced").click(function(){
    var advancedMode = ($(this).attr("checked") === undefined ? false : true);
    if(advancedMode){
      $(".settings div.holder p span").fadeIn();
    } else {
      $(".settings div.holder p span").fadeOut();
    }
  });
  $(".settings div.holder p span.next").click(function(){
    if(!animate){
      animate=true;
      $(".settings:nth("+visibleField+")").animate({
          opacity: 0
          },250,'linear',function(){
            $(".settings:nth("+visibleField+")").css("display","none");
            visibleField++;
            $(".settings:nth("+visibleField+")").css("opacity","0");
            $(".settings:nth("+visibleField+")").css("display","block");
            $(".settings:nth("+visibleField+")").animate({
              opacity: 1
            },250,'linear',function(){animate=false;});
          }
      );
    }
  });
  $(".settings div.holder p span.prev").click(function(){
    if(!animate){
      animate=true;
      $(".settings:nth("+visibleField+")").animate({
          opacity: 0
          },250,'linear',function(){
            $(".settings:nth("+visibleField+")").css("display","none");
            visibleField--;
            $(".settings:nth("+visibleField+")").css("opacity","0");
            $(".settings:nth("+visibleField+")").css("display","block");
            $(".settings:nth("+visibleField+")").animate({
              opacity: 1
            },250,'linear',function(){animate=false;});
          }
      );
    }
  });
  
  $("dd div").css("display","none");
  $("dd div").css("border","red solid 1px");
  $("dd div").css("min-height","50px");
  $("dd div").css("width","250px");
  $("dd div").css("margin","5px");
  $("dd div").css("position","absolute");
  $("dd div").css("background","#DCDCDC");
  $("dd span.help").html("<img src='/install/img/help.png' alt='?' style='vertical-align: middle;' />");
  $("dd span.help").css("cursor","pointer");
  $("dd span.help").click(function(){
    if(openInfo != null){
      openInfo.fadeOut('',function(){
        openInfo.children(".close").empty();
        openInfo=null
      });
    }
    infoField = $(this).parent().children("div");
    infoField.prepend("<span class='close'>[X]</span>");
    $(".close").click(function(){
    if(openInfo != null){
      openInfo.fadeOut('',function(){
        openInfo.children(".close").empty();
        openInfo=null
      });
    }
  });
    infoField.fadeIn('',function(){
      openInfo = infoField;
    });
  });

}
function step2(){
  setTimeout(function(){$("#nextStep").submit()},2000);
}
function step3(){
  if($("#override").length==0){
    setTimeout(function(){$("#nextStep").submit()},2000);
  }
  $("#override").click(function(){
    var checked = ($(this).attr("checked") === undefined ? false : true);
    if(checked) {
      alert("You are about to delete your old CMS!");
    }
  });
}
function step4(){
  $(".createInfo").css("display","none");
  $("#adminName").blur(function(){
    name = jQuery.trim($(this).attr("value"));
    $(this).attr("value",name);
    if(name.length>=4 || name.length==0){
      $(this).css('color',"green");
      $("#" + $(this).attr("id") + "Info").fadeOut();
    } else {
      $(this).css('color',"red");
      $("#" + $(this).attr("id") + "Info").fadeIn();
    }
  });
  $("#adminEmail").blur(function(){
    mail = jQuery.trim($(this).attr("value"));
    $(this).attr("value",mail);
    if(checkMail(mail) || mail.length==0){
      $(this).css('color',"green");
      $("#" + $(this).attr("id") + "Info").fadeOut();
    } else {
      $(this).css('color',"red");
      $("#" + $(this).attr("id") + "Info").fadeIn();
    }
  });
  $("#adminEmailCheck").blur(function(){
    mail2 = jQuery.trim($(this).attr("value"));
    mail1 = jQuery.trim($("#adminEmail").attr("value"));
       $(this).attr("value",mail2);
    if(mail1 == mail2 || mail2.length==0){
      $(this).css('color',"green");
      $("#" + $(this).attr("id") + "Info").fadeOut();
    } else {
      $(this).css('color',"red");
      $("#" + $(this).attr("id") + "Info").fadeIn();
    }
  });
  $("#adminPass").blur(function(){
    pass = jQuery.trim($(this).attr("value"));
    if(pass.length>=8 || pass.length==0){
      $(this).css('color',"green");
      $("#" + $(this).attr("id") + "Info").fadeOut();
    } else {
      $(this).css('color',"red");
      $("#" + $(this).attr("id") + "Info").fadeIn();
    }
  });
    $("#adminPassCheck").blur(function(){
      pass2 = jQuery.trim($(this).attr("value"));
      pass1 = jQuery.trim($("#adminPass").attr("value"));
       $(this).attr("value",pass2);
    if(pass1 == pass2 || pass2.length==0){
      $(this).css('color',"green");
      $("#" + $(this).attr("id") + "Info").fadeOut();
    } else {
      $(this).css('color',"red");
      $("#" + $(this).attr("id") + "Info").fadeIn();
    }
  });
  $("#adminName").focus(function(){
    $(this).css('color',"black");
  });
  $("#adminEmail").focus(function(){
    $(this).css('color',"black");
  });
  $("#adminEmailCheck").focus(function(){
    $(this).css('color',"black");
  });
  $("#adminPass").focus(function(){
    $(this).css('color',"black");
  });
  $("#adminPassCheck").focus(function(){
    $(this).css('color',"black");
  });
}
function step5(){
  
}


function checkMail(email){
  var pattern = new RegExp(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i);
  return pattern.test(email);
}

