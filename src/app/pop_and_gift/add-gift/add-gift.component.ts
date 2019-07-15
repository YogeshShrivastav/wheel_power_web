import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-add-gift',
  templateUrl: './add-gift.component.html',
  animations: [slideToTop()]
})
export class AddGiftComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
