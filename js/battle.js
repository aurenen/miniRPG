//Hints:  to make an element visible or hidden
//document.getElementById("THE_ID_FOR_FOR_THE_TAG").style.visibility= "hidden";
//document.getElementById("THE_ID_FOR_FOR_THE_TAG").style.visibility= "visible";

// variables that hold running total of each score
var finalHumanTotal = 0;
var finalComputerTotal = 0;

    //Global Values
    var finalHumanScore = 0;
    var finalComputerScore = 0;

function enemy()
{
  if (myCurrentHP > 0)
    myCurrentHP -= 10;
  document.getElementById('playerHP').innerHTML = enemyCurrentHP;
  document.getElementById('playerHPbar').style.width= enemyCurrentHP + "%";

  document.getElementById('battle-text').innerHTML = "<b>You</b> are hit for 10 hp";
  document.getElementById('battle-text').style.background= "Tomato";
  if (enemyCurrentHP == 0)
    alert("You lose!");
}

function attack() {
  if (enemyCurrentHP > 0)
    enemyCurrentHP -= 10;
  document.getElementById('monsterHP').innerHTML = enemyCurrentHP;
  document.getElementById('monsterHPbar').style.width= enemyCurrentHP + "%";

  document.getElementById('battle-text').innerHTML = "<b>Enemy</b> hit for 10 hp";
  document.getElementById('battle-text').style.background= "SpringGreen";
  if (enemyCurrentHP == 0)
    alert("You win!");
  else
    setTimeout(enemy, 1000);

}
function play()
{
  // create dice array
  var humanDice = new Array();
  var computerDice = new Array();
  
  // totals
  var humanTotal = 0;
  var computerTotal = 0;
  
  // output
  var out = "";
  
  // ROLL
  // roll the dice 6 times and calc running total
  //
  for (var i = 0; i < 6; i++)
  {
    humanDice[i] = roll();
    humanTotal += humanDice[i];

    
    computerDice[i] = roll();
    computerTotal += computerDice[i];

  }
  
    finalHumanScore += humanTotal;
    finalComputerScore += computerTotal;

  // display human dice
  out = displayDice(humanDice);
  document.getElementById('humanDice').innerHTML = out;
  
  // display computer dice
  out = displayDice(computerDice);
  document.getElementById('computerDice').innerHTML = out;
  
  document.getElementById('humanTotal').innerHTML = finalHumanScore;
  document.getElementById('computerTotal').innerHTML =  finalComputerScore;  
 
  document.getElementById("computerTotal").style.visibility= "hidden";
  document.getElementById("computerDice").style.visibility= "hidden";
  //What to do for HW
  // - Use style to make computer's total/dice hidden  (visibility: hidden;)
  // - For each roll, update the global variables finalHumanTotal
  //   and finalComputerTotal
  // - When user pushes button 'Stand', calculate winner
  //   - create a new function called calcWinner triggered by onClick from 
  //     the "stand" button

  // Calculate Winner --> function calcWinner() {

}

function displayDice(dice)
{
  var out = "";
  for (var i = 0; i < dice.length; i++)
  {
    out += "<img src='img/die" + dice[i] +".png' />";
  }
  
  return out;
}

    /* 
       This executes one roll for human and computer 
       The homework needs to implement 6 dice being rolled at once.
       To implement this, consider using an array for humanDice and 
       computerDice which could hold multiple values, and a for loop.
       For each roll, 6 total, have humanDice[i] = roll(); Still within 
       the for loop, update the running total of humanTotal 
       (humanTotal += humanDice[i])

       The same will be done for the computer, except that the style.visibility
       of the computerDice and computerTotal will be set to hidden until we 
       calculate the winner.

       Once the user hits the 'stand' button, we must calcWinner
       Use the global variables finalHumanScore/ finalComputerScore
       to compare with a series of 
       logical expressions and if statements will compare the two values.
       for the homework, firstly we should look to see if you and/or the 
       computer busted (greater than 100), if both values are under a 100, 
       then we compare whether hScore or cScore is greater.

       if(hScore > 100 && cScore >100) 
         // TIE

   if(hScore <100 && cScore < 100) {
     
     if(hScore > cScore)
       // You Win
     else if(hScore < cScore)
       // YOU Lose
     else
       // TIE

       }

     */
    

  function calcWinner() {
      var hScore = finalHumanScore;
      var cScore = finalComputerScore;


      document.getElementById('humanTotal').innerHTML = hScore;
      document.getElementById('computerTotal').innerHTML = cScore;

  document.getElementById("computerTotal").style.visibility= "visible";
  document.getElementById("computerDice").style.visibility= "visible";

  if(hScore > 100 && cScore >100) {
    document.getElementById('winner').innerHTML = "Tie!";
    }

  else if(hScore < 100 && cScore < 100) {
      if(hScore > cScore) {
    document.getElementById('winner').innerHTML = "You Win!";
    finalHumanTotal++
      }
      else if(hScore < cScore) {
    document.getElementById('winner').innerHTML = "You Lose!";
    finalComputerTotal++
      }
      else {
    document.getElementById('winner').innerHTML = "Tie!";
      }
    }


  else if(hScore > 100) {
    document.getElementById('winner').innerHTML = "You Lose!";
    finalComputerTotal++
    }


  else {
    document.getElementById('winner').innerHTML = "You Win!";
    finalHumanTotal++
    }


      document.getElementById('hTotal').innerHTML = finalHumanTotal;
      document.getElementById('cTotal').innerHTML = finalComputerTotal;

      //reset global scores
      finalHumanScore = 0;
      finalComputerScore = 0;
  }
