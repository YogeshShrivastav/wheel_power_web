import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-add-leave-rules',
  templateUrl: './add-leave-rules.component.html',
  animations: [slideToTop()]
})
export class AddLeaveRulesComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
