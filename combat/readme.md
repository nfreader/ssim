#Combat
##Definitions

**Protagonist(protag)**  
The _protagonist_ is the player that is _initiating the combat session_.

**Antagonist(antag)**  
The _antagonist_ is the player that is _being attacked during the combat session_.

**Tick(tick)**  
A _tick_ is one round of combat, during which the _attacker_ attempts to fire on the _defender_ and the _defender_ attempts to _evade_.

Each tick, a `rand()` is cast to determine who is _attacking_ and _defending_ each tick.

If `rand` comes out to 0, the _protag_ is set as the _attacker_ and the _antag_ is set as the _defender_.

If `rand` comes out to 1, the _antag_ is set as the _attacker_ and the _protag_ is set as the _defender_.

The _protag_ is always the _attacker_ for the first _tick_. 

**Attacker(atk)**  
Each _tick_, the _attacker_ fires all available weapons at the _defender_.

**Defender(def)**  
Each _tick_, the _defender_ rolls for a chance to avoid all or some of all of the _attacker_'s firepower. If the _defender_'s _armor_ reaches a certain (user-determined threshold), the _defender_ may also roll to _flee_.
