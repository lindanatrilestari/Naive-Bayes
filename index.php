<?php
error_reporting(0);
$tweet_1=null;

  class Type{
    const POSITIVE = 'Positive';
    const NEGATIVE = 'Negative';
  }
  
  class Classifier{
    
     private $types = [Type::POSITIVE, Type::NEGATIVE];
     private $words = [Type::POSITIVE => [], Type::NEGATIVE => []];
     private $documents = [Type::POSITIVE => 0, Type::NEGATIVE => 0];
     public function guess($statement){
       $words = $this->getWords($statement); // get the words
       //print_r($words);
       $best_likelihood = 0;
       $best_type = null;
       foreach ($this->types as $type) {
         $likelihood = $this->pTotal($type); // calculate P(Type)
         // echo $likelihood . "<br/>";
         foreach ($words as $word) {
          $likelihood *= $this->p($word, $type); // calculate P(word, Type)
         }
         if ($likelihood > $best_likelihood) {
          $best_likelihood = $likelihood;
          $best_type = $type;
         }
       }
       return $best_type;
     }
     public function learn($statement, $type){
       $words = $this->getWords($statement);
       foreach ($words as $word) {
       if (!isset($this->words[$type][$word])) {
        $this->words[$type][$word] = 0;
       }
        $this->words[$type][$word]++; // increment the word count for the type
       }
        $this->documents[$type]++; // increment the document count for the type
     }
     public function p($word, $type){
       $count = 0;
       if (isset($this->words[$type][$word])) {
        $count = $this->words[$type][$word];
       }
        return ($count + 1) / (array_sum($this->words[$type]) + 1);
       }
    public function pTotal($type){
       return ($this->documents[$type] + 1) / (array_sum($this->documents) + 1);
    }
    public function getWords($string){
       return preg_split('/\s+/', preg_replace('/[^A-Za-z0-9\s]/', '', strtolower($string)));
    }
  }
    
  $classifier = new Classifier();
  // read training files
  $file = fopen("training.txt","r");
  $regex = ":::";
  $list_tweet = [];
  while(! feof($file)){
    $line = fgets($file);
    if (!empty($line)) {
      $text = explode($regex,$line);
       if(trim(Type::POSITIVE) == trim($text[1])){
        $classifier->learn($text[0], Type::POSITIVE);
       }else if(trim(Type::NEGATIVE) == trim($text[1]) ){
        $classifier->learn($text[0], Type::NEGATIVE);
       }
     }
  }
  $classifier->learn('Symfony is the best', Type::POSITIVE);
  $classifier->learn('PhpStorm is great', Type::POSITIVE);
  $classifier->learn('Iltar complains a lot', Type::NEGATIVE);
  $classifier->learn('No Symfony is bad', Type::NEGATIVE);
  //Test Tweet
  // $tweet_1 = "Symfony is the great";
  // $tweet_2 = "i have bad day";
  if(isset($_POST['tweet_1'])){
  $tweet_1 = $_POST['tweet_1'];
  echo $tweet_1 . " , sentiment : " . $classifier->learn($tweet_1,Type::NEGATIVE) . "<br/>";
  }
  // echo "text : " . $tweet_2 . " , sentiment : " . $classifier->guess($tweet_2) . "<br/>";
?>


<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Sentiment Analysis - Linda Natri Lestari</title>

      <link rel="stylesheet" href="css/style.css">

</head>
<body>
  <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet' type='text/css'>
<link href="https://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">

<div id="content">
    <h1>Naive Bayes</h1>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" autocomplete="on">

		<!-- untuk sentence test -->
    <p>
 		   <tr>
        <td>
            <label for="sentence" class="icon-comment"> Sentence Test
            </label>
            <input type="text" name="tweet_1" id="sentence" placeholder="Inputkan Kalimat Anda disini" required="required">
            <!-- <textarea placeholder="Inputkan kalimat Anda disini " required="required"></textarea> -->
            </td>
        </tr>
        </p>

        <tr>
          <?php if($tweet_1): ?>
          <td><?php echo $tweet_1; ?></td>
          <?php endif; ?>
        </tr>
		<!-- akhir sentence test -->

		<!-- button submit -->
     <tr>
        <input type="submit" value=" Submit ! " />
      </tr>
		<!-- akhir button submit -->

		<!-- untuk hasil sentiment -->
        <p>
            <label for="sentiment" class="icon-bullhorn"> Sentiment</label>
            <input type="text" name="sentiment" id="sentiment" value="<?php echo $classifier->guess($tweet_1); ?>" />
        </p>
        <!-- akhir hasil sentiment -->

    </form>
</div>

</body>
</html>

