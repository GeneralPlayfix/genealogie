$(document).ready(function(){
    // $(".btn").next("div").hide(); 
    // $(".btn").click(function(){
    //     if($(this).next("div").is(":hidden")){
    //         $(".btn").next("div:visible").slideUp();
    //         $(this).next("div").slideDown();
    //     }
    //     else {
    //          $(this).next("div").slideUp();
    //     }
    // });
    
    $(".advancedSearch").next("div").hide(); 
    $(".advancedSearch").click(function(){
        if($(this).next("div").is(":hidden")){
            $(".advancedSearch").next("div:visible").slideUp();
            $(this).next("div").slideDown();
        }
        else {
             $(this).next("div").slideUp();
        }
    }); 

    
});
