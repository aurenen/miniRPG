function spcost(points) 
{
  if (myCurrentSP > 0)
    myCurrentSP -= points;
  // else
  //   alert("You ran out of SP!");
  document.getElementById('playerSP').innerHTML = myCurrentSP;
  document.getElementById('playerSPbar').style.width = myCurrentSP / myTotalSP * 100 + "%";
}

function enemy()
{
  var rnd = Math.floor((Math.random())*10); // [1-10]
  if (myCurrentHP > 0)
    // myCurrentHP -= 10;
    myCurrentHP -= rnd;
  document.getElementById('playerHP').innerHTML = enemyCurrentHP;
  document.getElementById('playerHPbar').style.width= enemyCurrentHP / enemyTotalHP * 100 + "%";

  document.getElementById('battle-text').innerHTML = "<b>You</b> are hit for " + rnd + " hp";
  document.getElementById('battle-text').style.background = "Tomato";
  if (enemyCurrentHP == 0)
    alert("You lose!");
}

function attack() {
  var rnd = Math.floor((Math.random())*10) + 1; // [1-10]
  if (enemyCurrentHP > 0) {
    enemyCurrentHP -= rnd;
    spcost(1);
  }
  document.getElementById('monsterHP').innerHTML = enemyCurrentHP;
  document.getElementById('monsterHPbar').style.width= enemyCurrentHP / myTotalHP * 100 + "%";

  document.getElementById('battle-text').innerHTML = "<b>Enemy</b> hit for " + rnd + " hp";
  document.getElementById('battle-text').style.background = "SpringGreen";
  if (enemyCurrentHP <= 0)
    alert("You win!");
  else
    setTimeout(enemy, 1000);

}
