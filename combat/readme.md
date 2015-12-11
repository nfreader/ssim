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
Each _tick_, the _defender_ rolls for a chance to avoid all or some of all of the _attacker_'s firepower. If the _defender_'s _armor_ reaches a certain (user-determined threshold), the _defender_ rolls again to _flee_.

**Damage**  
_Damage_ is calculated by multiplying the outfit's value by the outfit's quantity: `$damage = $outfit->quantity * $outfit->value`.

Damage is added to the vessel's `shielddam` until it reaches or passes the vessel's ship's `shield` value. Once that threshold has been reached, damage is added to the vessel's `armordam`.

Once `armordam` reaches or passes the vessel's ship's `armor` value, the vessel is `destroyed`.

**Rounds**  
Some outfits fire `rounds`, as determined in the SQL query. If the outfit doesn't have enough rounds (≤ 0), the outfit will not fire. Outfits that do not require ammo to fire will have their `usesammo` property set to `false`.

**Reload**  
Some outfits have to reload for a predetermined number of `tick`s before they can fire. If an outfit's `reload` property is greater than 1 (>1), the outfit will have to reload. The outfit's `charge` property is set to 0. On each `tick`, the outfit's `charge` property is checked. If it equals or exceeds the outfit's `reload` property, the weapon can be fired. If not, the outfit's `charge` is increased by 1. Defending ships with outfits that require a reload will have their charge increased by one, every `tick`. Once the outfit has fired, the `charge` will be reset to 0. 
