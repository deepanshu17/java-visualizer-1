public class LinkedList {
   
   class Node {
      Node next;
      String name;
      Node(String initialName) {
         name = initialName;
      }
   }
   
   private Node first = null;
   
   public void threeKongs() {
      first = new Node("DK Sr.");
      first.next = new Node("DK");
      first.next.next = new Node("DK Jr.");
   }
   
   public void printAll() {
      for (Node current = first;
           current != null;
           current = current.next) {
         System.out.println(current.name);
      }
   }

   public static void main(String[] args) {
      LinkedList mc = new LinkedList();
      mc.threeKongs();
      mc.printAll();
   }
}

