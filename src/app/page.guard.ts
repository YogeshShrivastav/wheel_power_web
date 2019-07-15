import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { SessionService } from './session.service';
import { DatabaseService } from './database.service';

@Injectable({
  providedIn: 'root'
})
export class PageGuard implements CanActivate {

  whp_user:any = [];
    
  constructor(private router: Router, public session: SessionService,public db: DatabaseService) {
      console.log('Auth Guard');
  }

  canActivate(
    next: ActivatedRouteSnapshot,state: RouterStateSnapshot): Observable<boolean> | Promise<boolean> | boolean {

      this.session.GetSession()
      .subscribe(resp=>{
        this.whp_user=resp;
        console.log(state.url);
        console.log("stat");
        
      },error=>{
        console.log("error");
      });
  
      if(this.whp_user.whp_login)
      {
        // console.log(this.loc);
        console.log(this.whp_user);
        this.db.can_active="1";
        return true;
      }
      else{
        this.db.can_active = '';
        this.router.navigate([''], { queryParams: { returnUrl: state.url }});
        return false;
      }
  }
}
