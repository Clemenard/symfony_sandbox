$(document).ready(function(){

$('#import').click(function(){
  email=$('#appbundle_user_email').val()
  $.ajax({
    url: '/apiCall/'+email,
    method: 'get',
    dataType: 'json',
    success: function(request){
      if(request!==false){
        $('#danger').hide()
    $('#appbundle_user_firstName').val(request.first_name)
    $('#appbundle_user_lastName').val(request.last_name)
    $('#appbundle_user_avatar').val(request.avatar)}
    else{
      $('#danger').show().html("Cet utilisateur n'existe pas chez notre partenaire")
    }
    }
  });
})
  })
