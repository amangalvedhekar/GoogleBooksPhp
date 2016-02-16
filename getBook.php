<?php

if($_SERVER["REQUEST_METHOD"] == "POST")
   {
//the api key can be obtained from google developers console
define("API_KEY","");
define("URL", "https://www.googleapis.com/books/v1/volumes?q=isbn:");
      $bookInformation=array();
      $imageAvailable = false;
      $image="img/125x125.jpg";
      $error="";
      $description=base64_encode("No description available");
      $publisher="No publisher available";
   		$isbn=trim($_POST['ISBN']);
      if(!isset($isbn)||$isbn =="")
      {
        $error="Please enter the 10 or 13 digit ISBN number located at the back of the book";
        $valid = false;
      }
      else
      {
        $isbn= str_replace('-', '', $isbn); 
        if(!is_numeric($isbn))
        {
          $error="ISBN number should be numeric. You entered ".$isbn." is not numeric";
          $valid = false;
        }  
        elseif(strlen($isbn)==13 && $isbn[0]!='9')
        {
          $error="ISBN should start with 978..Please review the ISBN entered and try again..";
          $valid = false;
        }  
        elseif (!(strlen($isbn)==10 || strlen($isbn)==13))
        {
           $error="ISBN should be atleast 10 or 13 digits...You entered only ".strlen($isbn)." digits";
           $valid= false;
        }  
          
      }
      if(strlen($error)=="")
      {
        $url=URL.$isbn.'&key='.API_KEY;
        //print_r($url);
        $bookDetails = file_get_contents($url);
        $bookDetailsArray=json_decode($bookDetails, true);
        $totalItem=$bookDetailsArray['totalItems'];
       // print_r($bookDetailsArray);
        if($totalItem==1)
        {
          if(array_key_exists('imageLinks',$bookDetailsArray['items'][0]['volumeInfo'] ))$imageAvailable=true;
          $title=  $bookDetailsArray['items'][0]['volumeInfo']['title'];
          $authors=  @implode(",", $bookDetailsArray['items'][0]['volumeInfo']['authors']);    
          if($imageAvailable)
          {
            $image=$bookDetailsArray['items'][0]['volumeInfo']['imageLinks']['smallThumbnail'];
            
            $description=$bookDetailsArray['items'][0]['volumeInfo']['description'];
            $publisher=$bookDetailsArray['items'][0]['volumeInfo']['publisher'];
          }
          $publishedDate=$bookDetailsArray['items'][0]['volumeInfo']['publishedDate'];  
          $identifier=$bookDetailsArray['items'][0]['volumeInfo']['industryIdentifiers'][1]['identifier'];
          $identifierTen=$bookDetailsArray['items'][0]['volumeInfo']['industryIdentifiers'][0]['identifier'];
          $categories=@implode(",",$bookDetailsArray['items'][0]['volumeInfo']['categories']);
          $bookInformation['Title']=$title;
          $bookInformation['Authors']=$authors;
          $bookInformation['Thumbnail']=$image;
          $bookInformation['Description']=$description;
          $bookInformation['Publisher']=$publisher;
          $bookInformation['ISBN13']=$identifier;
          $bookInformation['ISBN10']=$identifierTen;
          $bookInformation['Categories']=$categories;
          $valid =true;
        }
        else
        {
          $error="No book found for entered ISBN";
          $valid =false;
        }
      }
      die(json_encode(array('valid'=>$valid,'information'=>$bookInformation)));
    }
?>
