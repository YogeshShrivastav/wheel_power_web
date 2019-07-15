import { Injectable } from '@angular/core';
import { HttpClient,HttpHeaders } from '@angular/common/http';



@Injectable({
  providedIn: 'root'
})
export class DatabaseService {

  // db_url="http://nextstep.net.in/wheel_power_new/wheel_power_api/index.php/";
  // download_url = "http://nextstep.net.in/wheel_power_new/wheel_power_api/uploads/";

  db_url="http://wheelpower.abacusdesk.com/wheel_power_api/index.php/";
  download_url = "http://wheelpower.abacusdesk.com/wheel_power_api/uploads/";

  can_active:any="";
  token_value:any;
  constructor(public http:HttpClient) { 
  }

  login(data:any,fn:any)
  {
    let header = new HttpHeaders();

    // this.header = this.header.append({'Content-Type': 'application/json'});
    console.log(header);    
    return this.http.post(this.db_url+fn,JSON.stringify(data),{ headers: header })
  }
  
  getData(data:any,fn:any)
  {
    let header = new HttpHeaders();
    return this.http.post(this.db_url+fn,JSON.stringify(data),{ headers:header })
  }

  get_login(data:any,fn:any)
  {
    let header = new HttpHeaders();

    header = header.append('Authorization','Bearer '+data);
    header = header.append("Content-Type", "application/x-www-form-urlencoded");
    return this.http.post(this.db_url+fn,JSON.stringify(data),{ headers:header })

  }

  uploadImage(request_data:any,fn:any)
  {
    let header = new HttpHeaders();

    header.append("Content-type",undefined);
    console.log(request_data);
    
    return this.http.post(this.db_url+fn,request_data,{ headers:header });
  }
  
  private token_data : any;
  set_token_data(value)
  {
    this.token_data = value;
  }
  get_token_data()
  {
    return this.token_data;
  }


}
