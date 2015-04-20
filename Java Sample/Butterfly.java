package student;
import java.util.List;
import java.util.ArrayList;
import danaus.*;



public class Butterfly extends AbstractButterfly {

 /**A TileState[][] with all values initialized as null.*/
 private TileState[][] ts; 
 private int count = 0;

 /**Returns a TileState array of all flyable tiles on the map using the
  * depth-first search algorithm. */
 public @Override TileState[][] learn() {
  ts = new TileState[this.getMapHeight()][this.getMapWidth()];
  dFS();
  System.out.println(count);
  return ts;
 }

 /**Uses the depth-first search algorithm to traverse all flyable nodes on the
  * map and represents the nodes in a TileState[][]. Butterfly ends in the position
  * it began.*/
 public void dFS() {
  refreshState();
  ts[state.location.row][state.location.col] = this.state;
  for (Direction d: danaus.Direction.values()) {
   refreshState();
   if (ts[danaus.Common.mod(state.location.row + d.dRow, getMapHeight())]
     [danaus.Common.mod(state.location.col + d.dCol, getMapWidth())] 
       == null) {
    try {
     fly(d, Speed.NORMAL);
     refreshState();
     dFS();
     fly(danaus.Direction.opposite(d), Speed.NORMAL);
    }
    catch (ObstacleCollisionException c){
     count += 1;
     ts[danaus.Common.mod(state.location.row + d.dRow, getMapHeight())]
       [danaus.Common.mod(state.location.col + d.dCol, getMapWidth())] 
         = state.nil;
    }
   }
  }

 }
 /**Butterfly collects only Flowers whose flowerId's are represented in flowerIds.*/
 public @Override void run(List<Long> flowerIds) {
  List<Aroma> oldAromaList = inBoth(flowerIds, state.getAromas());
  oldFlowerCollect(flowerIds);

  List<Long> flowerIdsListCopy = new ArrayList<Long>(flowerIds); 
  List<Long> newFlowerIdsList = new ArrayList<Long>(flowerIds); //will be updated to only have new flowers
  for (Long l: flowerIdsListCopy) {                      
   for (Aroma a: oldAromaList) {
    if (l == a.getFlowerId()) {
     int i = newFlowerIdsList.indexOf(l);
     newFlowerIdsList.remove(i);
    }
   }
  }
  refreshState();
  while (!newFlowerIdsList.isEmpty()) {
   refreshState();
   double compare = -10.0;
   Aroma aroma = null;
   for (Aroma a: state.getAromas()) {
    if (newFlowerIdsList.contains(a.getFlowerId()) && a.intensity > compare) {
     compare = a.intensity;
     aroma = a;
    }
   }
   findNewFlower(aroma.getFlowerId());
   newFlowerIdsList.remove(aroma.getFlowerId());
  }
 }

 /**Flies butterfly to all Flower's both discovered in learn and present in flowerIds, and
  * collects those Flowers. */
 private void oldFlowerCollect(List<Long> flowerIds) {
  List<Aroma> oldAromaList = inBoth(flowerIds, state.getAromas()); 
  List<Aroma> oldAromaListCopy = inBoth(flowerIds, state.getAromas()); 
  while (!oldAromaListCopy.isEmpty()) {  
   int n = 0;
   double compare = -10.0;
   for (Aroma a: oldAromaListCopy) {   
    if (a.intensity > compare) {
     compare = a.intensity;
     n = oldAromaList.indexOf(a);
    }
   }
   findOldFlower(oldAromaList.get(n));
   oldAromaListCopy.remove(oldAromaList.get(n));
  }
 }

 /**Flies butterfly to the flower object that has Aroma flowerAroma and collects that flower.
  * Precondition: Flower with Aroma flowerAroma is in ts.*/
 private void findOldFlower(Aroma flowerAroma) {
  Flower f = hasFlower(flowerAroma.getFlowerId());
  if (f != null) { //Base Case, the current tile has the Flower
   collect(f);
  }
  else {
   aromaFlyLearned(flowerAroma);
   findOldFlower(flowerAroma);
  }
 }

 /**Returns a Flower with flowerId flowerId, or null if the current tile does not have a 
  * Flower with flowerId flowerId.*/
 private Flower hasFlower(Long flowerId) {
  refreshState();
  for (Flower f: state.getFlowers()) {
   if (f.getFlowerId() == flowerId) { 
    return f;
   }
  }
  return null;
 }

 /**Flies a Butterfly one tile in the direction of the strongest Aroma intensity for flowerAroma.
  * Precondition: Flower with Aroma flowerAroma is in ts.*/
 private void aromaFlyLearned(Aroma flowerAroma) {
  double compare = -10.0; 
  Direction max = null;
  for (Direction d: danaus.Direction.values()) {
   TileState t = ts[danaus.Common.mod(state.location.row + d.dRow, getMapHeight())]
     [danaus.Common.mod(state.location.col + d.dCol, getMapWidth())];
   for (Aroma a: t.getAromas()) {
    if (a.getFlowerId() == flowerAroma.getFlowerId() && a.intensity >= compare) {
     compare = a.intensity;
     max = d;
    }
   } 
  }
  fly(max, Speed.NORMAL);
  refreshState();
 }

 /**Returns a list of Aromas in stateAromas whose corresponding flowerIds are also in flowerIds.*/
 private ArrayList<Aroma> inBoth(List<Long> flowerIds, List<Aroma> stateAromas) {
  ArrayList<Aroma> both = new ArrayList<Aroma>();
  for (Long l: flowerIds) {
   for (Aroma a: stateAromas) {
    if (l == a.getFlowerId()) {
     both.add(a);
    }
   }
  }
  return both;
 }

 /**Collects the Flower with flowerId flowerId.*/
 private void findNewFlower(Long flowerId) {
  refreshState();
  Flower f = hasFlower(flowerId);
  while (f == null) { //Flower is not at this spot
   aromaFly(flowerId);
   refreshState();
   f = hasFlower(flowerId);
  }
  collect(f);
 }

 /**Flies a butterfly one tile in the direction towards a stronger Aroma intensity
  * of the Aroma represented by flowerId. */
 private void aromaFly(Long flowerId) {
  Aroma aroma = null;
  for (Aroma a: state.getAromas()) {
   if (a.getFlowerId() == flowerId) {
    aroma = a;
   }
  }
  double intensity = aroma.intensity;
  for (Direction d: danaus.Direction.values()) {
   if (ts[danaus.Common.mod(state.location.row + d.dRow, getMapHeight())]
     [danaus.Common.mod(state.location.col + d.dCol, getMapWidth())] != state.nil 
     && (ts[danaus.Common.mod(state.location.row + d.dRow, getMapHeight())]
       [danaus.Common.mod(state.location.col + d.dCol, getMapWidth())]).location != state.location) {
    fly(d, Speed.NORMAL); 
    refreshState();
    for (Aroma a: state.getAromas()) {
     if (a.getFlowerId() == flowerId && a.intensity > intensity) {
      return;
     }
     else if (a.getFlowerId() == flowerId && a.intensity <= intensity) {
      try { 
       if (ts[danaus.Common.mod(state.location.row + d.dRow, getMapHeight())]
         [danaus.Common.mod(state.location.col + d.dCol, getMapWidth())] != state) {
        fly(danaus.Direction.opposite(d), Speed.NORMAL);
        refreshState();
       }

      }
      catch (ObstacleCollisionException o) {
      }
     }
    }
   }
  }
 }
}