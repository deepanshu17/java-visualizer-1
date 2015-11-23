public class passby {
   
   static void reset(int x) {
      x = 0;
   }
   
   static void reset(int[] x) {
      for (int i : x) 
         i = 0;
   }
   
   static void reallyReset(int[] x) {
      for (int i=0; i<x.length; i++)
         x[i] = 0;
   }
   
   public static void main(String[] args) {
      int a = 3;
      int[] arr = {5, 10, 15};
      reset(a); 
      System.out.println(a);
      reset(arr); 
      System.out.println(java.util.Arrays.toString(arr));
      reallyReset(arr); 
      System.out.println(java.util.Arrays.toString(arr));
   }
   
}
