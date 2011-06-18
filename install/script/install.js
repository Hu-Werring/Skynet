/**
 * Installer
 */
$(document).ready(function() {
  var path = $(location).attr('pathname');
  var step = path.substr(1).split("/")[2];
  
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
    case '3':
      step4();
      break;
  }
});

function step1(){
  var visibleField = 0;
  var fields = $(".settings").length;
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
  $("#submitButton").css("width","30%");
  $("#submitButton").css("text-align","right");
  $("#submitButton").css("margin-left","auto");
  $("#submitButton").css("margin-right","auto");
  $("#goAdvanced").click(function(){
    var advancedMode = ($(this).attr("checked") === undefined ? false : true);
    if(advancedMode){
      $(".settings div.holder p span").css("display","block");
    } else {
      $(".settings div.holder p span").css("display","none");
    }
  });
  $(".settings div.holder p span.next").click(function(){
  $(".settings:nth("+visibleField+")").animate({
      opacity: 0
      },500,'linear',function(){
        $(".settings:nth("+visibleField+")").css("display","none");
        visibleField++;
        $(".settings:nth("+visibleField+")").css("opacity","0");
        $(".settings:nth("+visibleField+")").css("display","block");
        $(".settings:nth("+visibleField+")").animate({
          opacity: 1
        },500,'linear');
      }
  );
  });
  $(".settings div.holder p span.prev").click(function(){
  $(".settings:nth("+visibleField+")").animate({
      opacity: 0
      },500,'linear',function(){
        $(".settings:nth("+visibleField+")").css("display","none");
        visibleField--;
        $(".settings:nth("+visibleField+")").css("opacity","0");
        $(".settings:nth("+visibleField+")").css("display","block");
        $(".settings:nth("+visibleField+")").animate({
          opacity: 1
        },500,'linear');
      }
  );
  });  
}
function step2(){
  $("#submitButton").css("width","30%");
  $("#submitButton").css("text-align","right");
  $("#submitButton").css("margin-left","auto");
  $("#submitButton").css("margin-right","auto");
}
function step3(){
  $("#submitButton").css("width","30%");
  $("#submitButton").css("text-align","right");
  $("#submitButton").css("margin-left","auto");
  $("#submitButton").css("margin-right","auto");
}
function step4(){
  
}
function step5(){}