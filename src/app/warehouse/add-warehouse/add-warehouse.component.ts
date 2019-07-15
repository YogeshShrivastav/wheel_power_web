import { Component, OnInit } from '@angular/core';
import { DatabaseService } from 'src/app/database.service';
import { SessionService } from 'src/app/session.service';
import { ActivatedRoute, Router } from '@angular/router';
import { DialogService } from 'src/app/dialog.service';

@Component({
  selector: 'app-add-warehouse',
  templateUrl: './add-warehouse.component.html',
})
export class AddWarehouseComponent implements OnInit {

  session_data:any={};
  abq_user:any={};
  warehouse_id:any=0;
  constructor(public db:DatabaseService,public session:SessionService,public router:Router,public dialog:DialogService,public route:ActivatedRoute) {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    console.log(this.abq_user.id);
   }
  data:any={};
  ngOnInit() {
    this.route.params.subscribe(resp=>{
      console.log(resp);
      this.warehouse_id=resp["id"];
      this.get_warehouse_detail();
    })
  }

  get_warehouse_detail(){
    this.db.getData(this.warehouse_id,"warehouse/warehouse_edit")
    .subscribe(resp=>{
      console.log(resp);
      
      if(resp){
        this.get_countries();
        this.data=resp;        
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

  
  MobileNumber(event: any) {
    const pattern = /[0-9\+\-\ ]/;
    let inputChar = String.fromCharCode(event.charCode);
    if (event.keyCode != 8 && !pattern.test(inputChar)) {
      event.preventDefault();
    }
  }


  addwarehouse()
  {
    this.data.created_by = this.abq_user.id;
    console.log(this.data);
    this.db.getData(this.data,"warehouse/submit_warehouse")
    .subscribe(resp=>{
      console.log(resp);
      if(resp["msg"] == 'success')
      {
        if(this.warehouse_id){
          this.dialog.success("Success","Warehouse Updated");
        }else{
          this.dialog.success("Success","Warehouse Created");
        }
        this.router.navigate(['/warehouse-list']);
      }
      else
      {
        this.dialog.error("Error in Creation");
      }
    })
  }

}
