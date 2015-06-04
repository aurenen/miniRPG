function spcost(points) 
{
  if (myCurrentSP > 0)
    myCurrentSP -= points;
  // else
  //   alert("You ran out of SP!");
  document.getElementById('playerSP').innerHTML = myCurrentSP;
  document.getElementById('playerSPbar').style.width = myCurrentSP / myTotalSP * 100 + "%";
}

function dodge(atk, def)
{
  atk = (Math.floor(Math.random() * 10) + 1) + atk - Math.floor(Math.random() * 10);
  def = (Math.floor(Math.random() * 10) + 1) + def - Math.floor(Math.random() * 10);
  if (def >= atk)
    return true;
  else
    return false;
}

function attackanimation(name, type)
{
  document.getElementById(name).className = "battle-" + type;
  setTimeout(function(){document.getElementById(name).className = ""}, 1000);
}

function enemyattack(m)
{
  document.getElementById("battle-enemy").src="images/ani/monster" + m + "_attack.gif";
  setTimeout(function(){document.getElementById("battle-enemy").src="images/ani/monster" + m + "_idle.gif"}, 1000);
}

function enemy()
{
  var dmg = Math.floor(((Math.random())*10) * myLevel); // [1-10]
  if (dodge(enemyAtk, myDef)) dmg = 0;
  if (myCurrentHP > 0 && !dodge(enemyAtk, myDef)) {
    // if (!dodge(enemyAtk, myDef))
      myCurrentHP -= dmg;
  }
  if (myCurrentHP < 0) myCurrentHP = 0;

  if ( dmg != 0 && !dodge(enemyAtk, myDef) ) {
    document.getElementById('battle-text').innerHTML = "<b>You</b> are hit for " + dmg + " hp";
    attackanimation('battle-attack-user', 'slash');
    enemyattack(enemyId);
  }
  else  
    document.getElementById('battle-text').innerHTML = "<b>You</b> dodged!";
  document.getElementById('battle-text').style.background = "Tomato";
  document.getElementById('playerHP').innerHTML = myCurrentHP;
  document.getElementById('playerHPbar').style.width= myCurrentHP / myTotalHP * 100 + "%";

  if (myCurrentHP == 0) {
    alert("You lose!");
    window.location="battle.php";
  }
}

function attack(factor) {
  var dmg = Math.floor(((Math.random())*10) + 1 * factor * myLevel); // [1-10]
  if (dodge(myAtk, enemyDef)) dmg = 0;
  if (enemyCurrentHP > 0) {
    spcost(5 * factor);
    if (myCurrentSP <= 0) {
      alert("Out of SP!");
      myCurrentSP = 0;
    }
    else if (!dodge(myAtk, enemyDef))
      enemyCurrentHP -= dmg;
  }
  if (enemyCurrentHP < 0) enemyCurrentHP = 0;
  if (!dodge(myAtk, enemyDef)) {
    document.getElementById("battle-user").src="images/ani/" + myClass + "_attack.gif";
    setTimeout(function(){document.getElementById("battle-user").src="images/ani/" + myClass + "_idle.gif"}, 1000);
  }

  if (myCurrentSP > 0) {
    if ( dmg != 0 && !dodge(myAtk, enemyDef) ) {
      document.getElementById('battle-text').innerHTML = "<b>Enemy</b> hit for " + dmg + " hp";
      attackanimation('battle-attack-monster', 'fire');
    }
    else
      document.getElementById('battle-text').innerHTML = "<b>Enemy</b> dodged!";
    document.getElementById('battle-text').style.background = "SpringGreen";
  }

  document.getElementById('monsterHP').innerHTML = enemyCurrentHP;
  document.getElementById('monsterHPbar').style.width= enemyCurrentHP / enemyTotalHP * 100 + "%";

  if (enemyCurrentHP == 0) {
    alert("You win!");
    document.getElementById("battle-play").submit();
  }
  else
    setTimeout(enemy, 1000);

}

function recoverhp()
{
  spcost(5);
  if (myCurrentSP <= 0) {
    alert("Out of SP!");
    myCurrentSP = 0;
  }
  else {
    myCurrentHP += myTotalHP * .2;
    if (myCurrentHP > myTotalHP) myCurrentHP = myTotalHP;
    document.getElementById('playerHP').innerHTML = myCurrentHP;
    document.getElementById('playerHPbar').style.width= myCurrentHP / myTotalHP * 100 + "%";
  }
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
