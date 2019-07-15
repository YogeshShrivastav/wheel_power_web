import { Component, ElementRef, HostListener, Input, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import {ActivatedRoute, Router} from '@angular/router';
import { SessionService } from 'src/app/session.service';
import { FormControl } from '@angular/forms';
import { DialogService } from 'src/app/dialog.service';


@Component({
  selector: 'app-add-manufacture',
  templateUrl: './add-manufacture.component.html',
  animations: [slideToTop()]

})
export class AddManufactureComponent implements OnInit {

  constructor(public db:DatabaseService,public route:ActivatedRoute,private router: Router,public session:SessionService,public dialog:DialogService) { 
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value
    console.log(this.data);    
  }

  data:any={name:''};
  session_data:any={};
  abq_user:any={};
  user_id:any="";
  manufacturer_id;
  nexturl: any;
  myControl: FormControl;
  ngOnInit() {
    this.route.params.subscribe(resp=>{
      console.log(resp);
      this.manufacturer_id=resp["id"];
      this.get_manufacturer_detail();
    })  
  }  
  get_manufacturer_detail(){
    this.db.getData(this.manufacturer_id,"manufacturers/edit")
    .subscribe(resp=>{
      if(resp['0']){
        console.log('manufacturer_detail');
        this.get_countries();
        this.data=resp['0'];        
        console.log(resp);
        if(this.data.country_id==99){
         this.get_district();
         this.get_city();
         this.get_pincode();
        }
      }else{
        this.get_countries();
        this.data.country_id=99; 
      }
    });
  }
  country_list:any=[];
  get_countries()
  {
    this.db.getData("","manufacturers/get_countries")
    .subscribe(resp=>{
      console.log(resp);
      this.country_list=resp;
      this.data.country_id=99;
      this.get_states();
    });
  }
  state_list:any=[];
  get_states()
  {
    if(this.data.country_id=='99'){   
      this.db.getData("","manufacturers/get_state")
      .subscribe(resp=>{
        console.log(resp);
        this.state_list=resp;
      });
    }
  }
  district_list:any=[];
  get_district()
  {
    console.log(this.data.state);
    if(this.data.country_id=='99'){   
      this.db.getData({"state":this.data.state},"manufacturers/get_district")
      .subscribe(resp=>{
        console.log(resp);
        this.district_list=resp;
      })
   }
  }
  city_list:any=[];
  get_city()
  {
    console.log(this.data.state);
    console.log(this.data.district);
    if(this.data.country_id=='99'){   
      this.db.getData({"state":this.data.state,"district":this.data.district},"manufacturers/get_city")
      .subscribe(resp=>{
        console.log(resp);
        this.city_list=resp;
      })
    }    
  }
  pin_list:any=[];
  get_pincode(){
    if(this.data.country_id=='99'){   
      this.db.getData({"state":this.data.state,"district":this.data.district},"manufacturers/get_pincode")
      .subscribe(resp=>{
        console.log(resp);
        this.pin_list=resp;
      })
    }
  }

  addManufecturer()
  {
    console.log(this.data);
    this.data.user_id=this.abq_user.id;
    this.db.getData(this.data,"manufacturers/store")
    .subscribe(resp=>{
      console.log(resp);  
      if(resp)
      {
        if(this.manufacturer_id){
          this.dialog.success("Success","Vendor Updated");
        }else{
          this.dialog.success("Success","Vendor Created");
        }   
        
        if(this.abq_user.login_type == 'admin')
        { this.nexturl = this.route.snapshot.queryParams['returnUrl'] || '/manufacture-list'; }
        else
        {
          if(this.manufacturer_id)
          {
            this.nexturl = this.route.snapshot.queryParams['returnUrl'] || '/manufacture-list'; 
          } 
          else
          {
            this.nexturl = this.route.snapshot.queryParams['returnUrl'] || '/add-stock'; 
          }
        }
        
        this.router.navigate([this.nexturl]);
      }
    });
  }

  MobileNumber(event: any) {
    const pattern = /[0-9\+\-\ ]/;
    let inputChar = String.fromCharCode(event.charCode);
    if (event.keyCode != 8 && !pattern.test(inputChar)) {
      event.preventDefault();
    }
  }

}
