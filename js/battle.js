function spcost(points) 
{
  if (myCurrentSP > 0)
    myCurrentSP -= points;
  else
    alert("You ran out of SP!");
  document.getElementById('playerSP').innerHTML = myCurrentSP;
  document.getElementById('playerSPbar').style.width = myCurrentSP * 10 + "%";
}

function enemy()
{
  if (myCurrentHP > 0)
    myCurrentHP -= 10;
  document.getElementById('playerHP').innerHTML = enemyCurrentHP;
  document.getElementById('playerHPbar').style.width= enemyCurrentHP + "%";

  document.getElementById('battle-text').innerHTML = "<b>You</b> are hit for 10 hp";
  document.getElementById('battle-text').style.background = "Tomato";
  if (enemyCurrentHP == 0)
    alert("You lose!");
}

function attack() {
  if (enemyCurrentHP > 0) {
    enemyCurrentHP -= 10;
    spcost(3);
  }
  document.getElementById('monsterHP').innerHTML = enemyCurrentHP;
  document.getElementById('monsterHPbar').style.width= enemyCurrentHP + "%";

  document.getElementById('battle-text').innerHTML = "<b>Enemy</b> hit for 10 hp";
  document.getElementById('battle-text').style.background = "SpringGreen";
  if (enemyCurrentHP == 0)
    alert("You win!");
  else
    setTimeout(enemy, 1000);

}
