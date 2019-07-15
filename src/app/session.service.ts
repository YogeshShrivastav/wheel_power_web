import { Injectable } from '@angular/core';
import { DatabaseService } from './database.service';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable,of } from 'rxjs';
import { DialogService } from './dialog.service';

@Injectable({
  providedIn: 'root'
})
export class SessionService {

  constructor(public route:ActivatedRoute,public db:DatabaseService,public router:Router,public dialog:DialogService) { }

  whp_user:any={};

  NextUrl:any='';
  setSession(data:any)
  {
    console.log("session");
    console.log(data);
    this.NextUrl = this.route.snapshot.queryParams['returnUrl'] || '/product-list';
    this.db.getData(data,"login/loginUser")
    .subscribe(resp=>{
      if(resp['data'] != null)
      {
        this.whp_user=resp['data'];
        this.whp_user.login_type=resp['login_type'];
        this.whp_user.whp_login=true;
        this.db.can_active="1";
        localStorage.setItem("whp_user",JSON.stringify(this.whp_user));

        if(this.whp_user.login_type == 'admin')
        { this.router.navigate([this.NextUrl]); }
        else
        { this.router.navigate(['/stock-list']); }
      }
      else
      { this.dialog.error("incorrect Username or Password"); } 
    })
  }

  GetSession():  Observable<any> 
  {
    this.whp_user = JSON.parse(localStorage.getItem('whp_user')) || [];
    console.log(this.whp_user);
    return of(this.whp_user);
  }

  LogOutSession()
  {
    this.whp_user = {};
    this.whp_user.whp_login = false;
    this.db.can_active = '';
    localStorage.removeItem('whp_user');
  }
}
