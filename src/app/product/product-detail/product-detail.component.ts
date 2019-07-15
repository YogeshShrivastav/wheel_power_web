import { Component, OnInit,Inject  } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { MatDialog,MatDialogConfig } from "@angular/material";
import { DialogBodyComponent } from '../../dialog-body/dialog-body.component';
import { ActivatedRoute } from '@angular/router';
import { DatabaseService } from 'src/app/database.service';
import { ToastrManager } from 'ng6-toastr-notifications';
import { DialogService } from 'src/app/dialog.service';
import { ProductDescDialogComponent } from '../product-desc-dialog/product-desc-dialog.component';

@Component({
  selector: 'app-product-detail',
  templateUrl: './product-detail.component.html',
  animations: [slideToTop()]
  
})
export class ProductDetailComponent implements OnInit {
  
  constructor(private dialog: MatDialog,public route:ActivatedRoute,public db:DatabaseService,public toastr:ToastrManager,public dialog_2:DialogService) { }
  
  product_id:any="";
  feat:any={};
  loader:any="";
  upload_url:any='';
  ngOnInit() {
    this.route.params.subscribe(resp=>{
      console.log(resp);
      this.product_id=resp["id"];
      this.get_product_detail();
      this.upload_url = this.db.download_url;
    })
  }
  
  formData=new FormData();
  
  openEditDialog(dialog_data,type,name)
  {
    // const dialogConfig = new MatDialogConfig();
    console.log(dialog_data);
    
    const dialogRef=this.dialog.open(DialogBodyComponent,{
      width: '500px',
      data:{
        product_id : this.product_id,
        product_val : dialog_data,
        product_type : type,
        caption : name
      }
    });
    
    dialogRef.afterClosed().subscribe(result => {
      this.get_product_detail();
    });
  }
  
  
  
  openDescDialog(dialog_data,type,name)
  {
    console.log(dialog_data);
    
    const dialogRef=this.dialog.open(ProductDescDialogComponent,{
      width: '1024px',
      data:{
        product_id : this.product_id,
        product_val : dialog_data,
        product_type : type,
        caption : name
      }
    });
    
    dialogRef.afterClosed().subscribe(result => {
      this.get_product_detail();
    });
  }
  
  product:any={};
  feature_list:any=[];
  feature_data:any=[];
  image_data:any=[];
  get_product_detail()
  {    
    this.loader="1"; 
    this.db.getData({"id":this.product_id},"products/edit")
    .subscribe(resp=>{
      console.log(resp);
      this.product=resp["product"];
      this.feature_list=resp["feature_list"];
      this.feature_data=resp["feature"];
      this.image_data=resp["image"];
      console.log(this.image_data.length);
      this.loader="";      
    });
  }
  active:any = {};
  toggleterritory(key,action)
  {
    console.log(action);
    console.log(key);
    
    if(action == 'open')
    { this.active[key] = true; }
    if(action == 'close')
    { this.active[key] = false;}
    
    console.log(this.active);
  }
  
  submitted:any=[];
  addfeature()
  {
    this.loader="1";      
    let new_arr=this.feat.value.split(",");
    for(var i=0; i<new_arr.length; i++)
    {
      this.submitted.push({"product_id":this.product_id,"feature":this.feat.feature,"value":new_arr[i] });
    }
    
    this.db.getData(this.submitted,"products/feature_add")
    .subscribe(resp=>{
      console.log(resp);
      this.loader="";  
      this.get_product_detail();
      this.toastr.successToastr("Updated","Success!");
    });
    console.log(this.submitted);
    
    this.feat={};
    new_arr="";
  }
  
  delete_feature(p_id,fea)
  {  
    this.loader="1";      
    console.log('prod---------');    
    console.log(p_id);
    console.log(fea);
    this.db.getData({'product_id':p_id,'feature':fea},"products/remove_feature")
    .subscribe(resp=>{
      console.log(resp);
      this.loader="";      
      this.get_product_detail();
      this.toastr.successToastr("Deleted","Success!");
      
    });
    
  }
  
  delete_feature_value(array,index,id){
    this.loader="1"; 
    console.log(index);
    array.splice(index,1) ; 
    this.db.getData(id,"products/remove_feature_value")
    .subscribe(resp=>{
      console.log(resp);
      this.loader=""; 
      this.toastr.successToastr("Deleted","Success!");
      this.get_product_detail();
    });
  }
  
  
  imageSrc: any={};
  urls = new Array<string>();
  selectedFile:any=[];
  selectvalue(data)
  {
    console.log(data);
    console.log(data.target.files.length);
    this.formData.append("product_id",this.product_id);
    for(var i=0; i<data.target.files.length; i++)
    {
      this.selectedFile.push(data.target.files[i]);
      this.formData.append("image"+i,data.target.files[i],data.target.files[i].name);
    }
    this.db.uploadImage(this.formData,"Products/add_image")
    .subscribe(resp=>{
      console.log(resp);
      if(resp)
      {
        this.get_product_detail();
        this.toastr.successToastr("Updated","Success!");
      }
    })
  }
  
  delete_image(id)
  {
    this.loader="1";
    this.dialog_2.delete("Image")
    .then(resp=>{
      if(resp)
      {
        this.db.getData(id,"products/delete_image")
        .subscribe(resp=>{
          console.log(resp);
          this.get_product_detail();
          this.loader="";
          this.toastr.successToastr("Deleted","Success!");
        })
      }
      this.loader="";
    })
    
  }
  
}
