import { Component, OnInit } from '@angular/core';
import { DatabaseService } from '../database.service';
import { Router } from '@angular/router';
import { DialogService } from '../dialog.service';

@Component({
  selector: 'app-brand',
  templateUrl: './brand.component.html',
})
export class BrandComponent implements OnInit {

  brand:any='';
  data:any={};
  constructor(public db:DatabaseService,public route:Router,public dialog:DialogService) { }
  formData=new FormData;
  ngOnInit() {
  }
  loader:any='';
  submit_brand()
  {
    this.loader="1";
    this.formData.append('image',this.selectedFile[0],this.selectedFile.name);
    this.formData.append("brand",JSON.stringify(this.brand));
    this.db.uploadImage(this.formData,"products/insert_brand_image")
    .subscribe(resp=>{
      console.log(resp);
      if(resp == 'success')
      {
        this.dialog.success("Success","Image Inserted");
        // this.route.navigate(['/product-list']);
        this.brand='';
        this.selectedFile = [];
      }
      this.loader="";
    })
  }

  selectedFile:any=[];
  selectFile(event)
  {
    this.selectedFile.push(event.target.files[0]);
    console.log(this.selectedFile);
  }
}
