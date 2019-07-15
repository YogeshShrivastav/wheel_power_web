import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import { AddressModalComponent } from '../address-modal/address-modal.component';
import {MatDialog} from '@angular/material';
import { ActivatedRoute } from '@angular/router';
import { ToastrManager } from 'ng6-toastr-notifications';
import { UserEditDialogComponent } from '../user-edit-dialog/user-edit-dialog.component';
@Component({
  selector: 'app-system-user-detail',
  templateUrl: './system-user-detail.component.html',
  animations: [slideToTop()]
})
export class SystemUserDetailComponent implements OnInit {

  constructor(public dialog: MatDialog,public db:DatabaseService,public route:ActivatedRoute,public toastr:ToastrManager) { }
  user_id:any="";
  user_data:any={};
  show_psw:boolean=false;
  show_userinput:boolean=false;
  show_userpsw:boolean=false;
  ngOnInit() {
    this.route.params.subscribe(resp=>{
      console.log(resp);
      this.user_id=resp["id"];
      this.get_user_detail();
      this.get_countries();
    });
  }
  openDialog() {
    const dialogRef = this.dialog.open(AddressModalComponent,{data:this.user_data});
    dialogRef.afterClosed().subscribe(result => {
      console.log(`Dialog result: ${result}`);
    });
  }
  open_edit(edit_item)
  {
    this.user_data.edit_item=edit_item;
    const dialog1 = this.dialog.open(UserEditDialogComponent,{data:this.user_data});
    dialog1.afterClosed().subscribe(result => {
      console.log(`Dialog result: ${result}`);
    });
  }
  get_user_detail(){
    this.db.getData({"id":this.user_id},"user/get_user_detail")
    .subscribe(resp=>{
      console.log(resp);
      this.user_data=resp['user_data']; 
      if(this.user_data.status==='Active'){
       this.user_data.user_status=true;
      }else{
       this.user_data.user_status=false;
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
    });
  }
  update_status:any={};
  user_allowed(id)
  {
    this.update_status.id=id;
    console.log(id);    
    console.log("yes");
    if(this.user_data.user_status)
    { this.update_status.status="Active"; }
    else
    { this.update_status.status="Deactive"; }
    this.db.getData(this.update_status,"User/update_status")
      .subscribe(resp=>{
        console.log(resp);
        this.get_user_detail();
        this.toastr.successToastr("Updated","Success!");
      });    
  }

  update_user(){
    console.log(this.user_data);
    this.db.getData(this.user_data,"User/update_user")
      .subscribe(resp=>{
        console.log(resp);
        this.show_psw=false;
        this.show_userinput=false;  
        this.show_userpsw=false;
        this.get_user_detail();        
      });
  }
  
}
