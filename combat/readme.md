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

##Gameplay

The `protag` will be able to initiate an attack against the `antag` if:

* The `protag`'s government is neutral or at war with the `antag`'s government
* Or, if the `protag` is aligned with the pirate government
* Or, if the `protag` doesn't mind a **significant** legal hit (among other player-induced penalties)

At all times, the `protag` and the `antag` must be in space (not landed, not in bluespace)

Once combat has been initiated, the `antag` will be notified. Accounting for various outfits and other factors, a countdown will begin. During this time, the `antag` will have the opportunity to change various settings in preparation for the battle. Additionally, the `antag` will be given the chance to do a 'blind jump', which will consume one fuel unit and put them in a random neighboring system. If the `antag` doesn't have enough fuel, they cannot jump. The `antag` cannot land or jump as they would normally.

If either the `protag` or `antag` are destroyed, the game will check for an escape pod outfit. If none is found, the pilot will be marked as deceased and the player will be invited to review their stats and create a new pilot.

If the pilot has an escape pod, they will 'respawn' on their homeworld after a countdown determined by what sort of escape pod they had. A certain percentage of their credits will also be depleted.

The victor will be returned to orbit with no further actions taken.

If the `defending` vessel successfully flees the battle, they will be placed in a random neighboring system.

###Post-battle
There are two kinds of battle reports:

* Public reports will show the per-`tick` shields and armor for the attacker and the defender, along with how much damage (if any) was inflicted.
* Private reports will show the above, along with any other outfit data.

Public reports are posted publicly on the kill-board. Private reports are given a UUID, the links to which will be automatically sent to both battle participants.
