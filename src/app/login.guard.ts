import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { SessionService } from './session.service';
import { DatabaseService } from './database.service';

@Injectable({
  providedIn: 'root'
})
export class LoginGuard implements CanActivate {

  constructor(private router: Router,public session: SessionService, public db: DatabaseService) {
  }

  whp_user:any={};
  canActivate(
    next: ActivatedRouteSnapshot,state: RouterStateSnapshot): Observable<boolean> | Promise<boolean> | boolean {
      this.session.GetSession()
      .subscribe(data=>{
        this.whp_user=data;
      },error=>{
        console.log("Error");
        
      });
  
      if(this.whp_user.whp_login)
      {
        console.log(this.whp_user);
        if(state.url!="/")
        {   }
        else
        {
          if(this.whp_user.login_type == 'admin')
          {
            this.router.navigate(["/product-list"])
          }
          else
          {
            this.router.navigate(["/stock-list"])
          }
        }
        this.db.can_active="1";
        return false;
      }
      else
      {
        this.db.can_active="";
        return true;
      } 
  }
}
