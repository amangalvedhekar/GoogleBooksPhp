 $(document).ready(function() {
          $("#getBookDetails").click(function() {
            $("#postBookModal").modal('show'); 
             $("#getBooksImage").find('h3').addClass('none'); 
          });
          $("#ISBNInput").keyup(function() {
            isbnRegex = "/^([0-9]+)$/";
            enteredValue = $("#ISBNInput").val().trim();
            count = enteredValue.length;
            if(bookISBN.validateISBN(enteredValue) && (count == 10 || count ==13))
            {
                bookISBN.addSuccess();
                 $(document).off('click').on('click', '#submitISBN', function(){
                 $.ajax({
                  type: "POST",
                  url: "getBook.php",
                  data: $("#getBookSearch").serialize(),
                  dataType: "json",
                  success: function(data)
                  {
                    
                    if(data.valid)
                    {
                      $("#ISBNInput").val("");
                      $("#postBookModal").modal('hide');
                    $("#bookDetailsModal").modal({backdrop:'static',keyboard: false});
                    $("#bookDetailsModal").find('.bookInfo').html("");
                     $.each( data.information, function(key,value){
                      if(key === 'Title')
                      {
                        $("#bookDetailsModal").find('h4').html(value);
                      }
                      else if(key ==='Thumbnail')
                      {
                        $("#bookDetailsModal").find('img').attr('src',value);
                      }
                      else
                      {
                        $("#bookDetailsModal").find('.bookInfo').append('<p><strong>'+key+': </strong>'+value+'</p>');
                      }
                     }

                      );
                    }
                    else
                    {
                      $("#getBooksImage").find('h3').removeClass('none').html("Sorry no book details found for entered ISBN try again");
                    }
                  },
                  error: function(data)
                  {
                   $("#getBooksImage").find('h3').removeClass('none').html("Technical glitches please try again later"); 
                  }
                  });
                  return false;
            });
            }
            else
            {
              bookISBN.addError();
            }
           
          });
          var bookISBN = {
            validateISBN: function (enteredValue) {
              var isbnRegex = /^([0-9]+)$/;
              return isbnRegex.test(enteredValue);
            },
            addError: function()  {
            $('#ISBNInput').parent('.form-group').removeClass('has-success').addClass('has-error');
            $('#submitISBN').removeClass('btn-success').addClass('btn-danger disabled');
          },
          addSuccess: function() {
            $('#ISBNInput').parent('.form-group').removeClass('has-error').addClass('has-success');
            $('#submitISBN').removeClass('btn-danger disabled').addClass('btn-success');
          }  
          }; 
        });