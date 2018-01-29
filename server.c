#include<netinet/in.h>    
#include<stdio.h>    
#include<stdlib.h>    
#include<sys/socket.h>    
#include<sys/stat.h>    
#include<sys/types.h>    
#include<unistd.h>
#include<string.h>
#include<sys/time.h>

int main() {    
   int create_socket, new_socket;    
   socklen_t addrlen;    
   int bufsize = 1024;    
   char *buffer = malloc(bufsize);    
   struct sockaddr_in address;    
 
   if ((create_socket = socket(AF_INET, SOCK_STREAM, 0)) > 0){    
      printf("The socket was created\n");
   }
    
   address.sin_family = AF_INET;    
   address.sin_addr.s_addr = INADDR_ANY;    
   address.sin_port = htons(15000);    
    
   if (bind(create_socket, (struct sockaddr *) &address, sizeof(address)) == 0){    
      printf("Binding Socket\n");
   }
    
    
   while (1) {    
      if (listen(create_socket, 10) < 0) {    
         perror("server: listen");    
         exit(1);    
      }    
    
      if ((new_socket = accept(create_socket, (struct sockaddr *) &address, &addrlen)) < 0) {    
         perror("server: accept");    
         exit(1);    
      }    
    
      if (new_socket > 0){  
         printf("The Client is connected...\n");
      }

      struct timeval now, then;
      gettimeofday(&now, NULL);

      FILE *fp = fopen("/Volumes/RAM/1.json", "r");
      size_t len = 255;
      char* out = malloc(sizeof(char) * len);
      fgets(out, len, fp);
      fclose(fp);

      recv(new_socket, buffer, bufsize, 0);    
      printf("%s\n", buffer);    
 
      write(new_socket, "HTTP/1.1 200 OK\n", 16);

      char* clen = malloc(20);
      sprintf(clen, "Content-Length: %zu\n", strlen(out));
      write(new_socket, clen, strlen(clen));

      write(new_socket, "Content-Type: application/json\n", 31);

      gettimeofday(&then, NULL);

      char* xbench = malloc(20);
      sprintf(xbench, "X-Benchmark: %d\n", (then.tv_usec - now.tv_usec));
      write(new_socket, xbench, strlen(xbench));

      write(new_socket, "\n", 2);
      write(new_socket, out, strlen(out));

      close(new_socket);    
   }    
   close(create_socket);    
   return 0;    
}