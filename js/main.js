$(document).ready(function(){
    console.log("ok");
    $('#search').keyup(function(){
        var search = $('#search').val();
        if(search!= ""){
            $.ajax({
                url:'./util/search.php',
                method:'POST',
                data:{search:search},
                success:function(data){
                    $('#content').html(data);
                }
            })
        }else{
            $('#content').html("");
        }
        $(document).on("click", "p", function(){
            $("#search").val($(this).text());
            $("#content").html('');
        })
    })
})