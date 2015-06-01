function spcost(points) 
{
  if (myCurrentSP > 0)
    myCurrentSP -= points;
  // else
  //   alert("You ran out of SP!");
  document.getElementById('playerSP').innerHTML = myCurrentSP;
  document.getElementById('playerSPbar').style.width = myCurrentSP / myTotalSP * 100 + "%";
}

function attackanimation(name)
{
  document.getElementById(name).className = "battle-fire";
  setTimeout(function(){document.getElementById(name).className = ""}, 1000);
}

function enemyattack()
{
  document.getElementById("battle-enemy").src="images/ani/no_repeat.gif";
  setTimeout(function(){document.getElementById("battle-enemy").src="images/ani/monster_idle.gif"}, 1000);
}

function enemy()
{
  var dmg = Math.floor((Math.random())*10); // [1-10]
  if (myCurrentHP > 0)
    myCurrentHP -= dmg;
  if (myCurrentHP < 0) myCurrentHP = 0;
  document.getElementById('playerHP').innerHTML = myCurrentHP;
  document.getElementById('playerHPbar').style.width= myCurrentHP / myTotalHP * 100 + "%";

  if (dmg == 0)
    document.getElementById('battle-text').innerHTML = "<b>You</b> dodged!";
  else {
    document.getElementById('battle-text').innerHTML = "<b>You</b> are hit for " + dmg + " hp";
    // attackanimation('battle-attack-user');
    enemyattack();
  }
  document.getElementById('battle-text').style.background = "Tomato";

  if (myCurrentHP == 0) {
    alert("You lose!");
    window.location="battle.php";
  }
}

function attack(factor) {
  var dmg = (Math.floor((Math.random())*10) + 1) * factor; // [1-10]
  if (enemyCurrentHP > 0) {
    enemyCurrentHP -= dmg;
    spcost(5);
  }
  if (enemyCurrentHP < 0) enemyCurrentHP = 0;
  document.getElementById("battle-user").src="images/ani/ranger_attack.gif";
  setTimeout(function(){document.getElementById("battle-user").src="images/ani/ranger_idle.gif"}, 1000);

  document.getElementById('monsterHP').innerHTML = enemyCurrentHP;
  document.getElementById('monsterHPbar').style.width= enemyCurrentHP / myTotalHP * 100 + "%";

  if (dmg == 0)
    document.getElementById('battle-text').innerHTML = "<b>Enemy</b> dodged!";
  else {
    document.getElementById('battle-text').innerHTML = "<b>Enemy</b> hit for " + dmg + " hp";
    attackanimation('battle-attack-monster');
  }
  document.getElementById('battle-text').style.background = "SpringGreen";

  if (enemyCurrentHP == 0) {
    alert("You win!");
    document.getElementById("battle-play").submit();
  }
  else
    setTimeout(enemy, 1000);

}

function recoverhp()
{
  myCurrentHP += myTotalHP * .2;
  if (myCurrentHP > myTotalHP) myCurrentHP = myTotalHP;
  document.getElementById('playerHP').innerHTML = myCurrentHP;
  document.getElementById('playerHPbar').style.width= myCurrentHP / myTotalHP * 100 + "%";
  setTimeout(enemy, 1000);
}

function recoversp()
{
  myCurrentSP += myTotalSP * .2;
  if (myCurrentSP > myTotalSP) myCurrentSP = myTotalSP;
  document.getElementById('playerSP').innerHTML = myCurrentSP;
  document.getElementById('playerSPbar').style.width= myCurrentSP / myTotalSP * 100 + "%";
  setTimeout(enemy, 1000);
}
