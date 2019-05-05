if(typeof slipLoaded === 'undefined'){
    window.Slip=function(){"use strict";function t(e,i){if("string"==typeof e&&(e=document.querySelector(e)),!e||!e.addEventListener)throw new Error("Please specify DOM node to attach to");if(!this||this===window)return new t(e,i);this.options=i=i||{},this.options.keepSwipingPercent=i.keepSwipingPercent||0,this.options.minimumSwipeVelocity=i.minimumSwipeVelocity||1,this.options.minimumSwipeTime=i.minimumSwipeTime||110,this.cancel=this.setState.bind(this,this.states.idle),this.onTouchStart=this.onTouchStart.bind(this),this.onTouchMove=this.onTouchMove.bind(this),this.onTouchEnd=this.onTouchEnd.bind(this),this.onMouseDown=this.onMouseDown.bind(this),this.onMouseMove=this.onMouseMove.bind(this),this.onMouseUp=this.onMouseUp.bind(this),this.onMouseLeave=this.onMouseLeave.bind(this),this.onSelection=this.onSelection.bind(this),this.onContainerFocus=this.onContainerFocus.bind(this),this.setState(this.states.idle),this.attach(e)}function e(t){var e=t.style[c];if(e)return{value:e,original:e};if(window.getComputedStyle){var i=window.getComputedStyle(t).getPropertyValue(u);if(i&&"none"!==i)return{value:i,original:""}}return{value:"",original:""}}function i(t,e){for(var i=0,n=0,o=0;o<e.length;o++)1===e[o].nodeType&&(n++,e[o]===t.node&&(i=n-1));return i}var n={ariaRole:"listbox",tabIndex:0,focus:!1},o={ariaRole:"option",tabIndex:-1,focus:!1},s=/Chrome\/[3-5]/.test(navigator.userAgent),a=s,r=s,h=document.createElement("div").style,l="transition"in h?"transition":"webkitTransition",c="transform"in h?"transform":"webkitTransform",u="webkitTransform"===c?"-webkit-transform":"transform",d="userSelect"in h?"userSelect":"webkitUserSelect";h[c]="translateZ(0)";var g=h[c]?"translateZ(0) ":"",m=h[c]?"translateZ(1px) ":"";h=null;var f=0,v=!1,p=function(){};return t.prototype={container:null,options:{},state:null,target:null,usingTouch:!1,mouseHandlersAttached:!1,startPosition:null,latestPosition:null,previousPosition:null,canPreventScrolling:!1,states:{idle:function(){return this.removeMouseHandlers(),this.target&&(this.target.node.style.willChange="",this.target=null),this.usingTouch=!1,{allowTextSelection:!0}},undecided:function(){if(this.target.height=this.target.node.offsetHeight,this.target.node.style.willChange=u,this.target.node.style[l]="",this.dispatch(this.target.originalTarget,"beforewait"))var t=setTimeout(function(){var t=this.getAbsoluteMovement();this.canPreventScrolling&&t.x<15&&t.y<25&&this.dispatch(this.target.originalTarget,"beforereorder")&&this.setState(this.states.reorder)}.bind(this),300);else this.dispatch(this.target.originalTarget,"beforereorder")&&this.setState(this.states.reorder);return{leaveState:function(){clearTimeout(t)},onMove:function(){var t=this.getAbsoluteMovement();if(t.x>20&&t.y<Math.max(100,this.target.height)){if(this.dispatch(this.target.originalTarget,"beforeswipe",{directionX:t.directionX,directionY:t.directionY}))return this.setState(this.states.swipe),!1;this.setState(this.states.idle)}if(t.y>20&&this.setState(this.states.idle),t.x>1.2*t.y)return!1},onLeave:function(){this.setState(this.states.idle)},onEnd:function(){var t=this.dispatch(this.target.originalTarget,"tap");return this.setState(this.states.idle),t}}},swipe:function(){function t(){n.classList.remove("slip-swiping-container")}var e=!1,n=this.container,o=i(this.target,this.container.childNodes);return n.classList.add("slip-swiping-container"),this.target.height=this.target.node.offsetHeight,{leaveState:function(){e?this.animateSwipe(function(e){if(e.node.style[c]=e.baseTransform.original,e.node.style[l]="",this.dispatch(e.node,"afterswipe"))return t(),!0;this.animateToZero(void 0,e)}.bind(this)):this.animateToZero(t)},onMove:function(){var t=this.getTotalMovement();if(Math.abs(t.y)<this.target.height+20)return this.dispatch(this.target.node,"animateswipe",{x:t.x,originalIndex:o})&&(this.target.node.style[c]="translate("+t.x+"px,0) "+g+this.target.baseTransform.value),!1;this.dispatch(this.target.node,"cancelswipe"),this.setState(this.states.idle)},onLeave:function(){this.state.onEnd.call(this)},onEnd:function(){var t=this.getAbsoluteMovement(),i=t.x/t.time,n=100*Math.abs((this.startPosition.x-this.previousPosition.x)/this.container.clientWidth),s=i>this.options.minimumSwipeVelocity&&t.time>this.options.minimumSwipeTime||this.options.keepSwipingPercent&&n>this.options.keepSwipingPercent;return s?this.dispatch(this.target.node,"swipe",{direction:t.directionX,originalIndex:o})&&(e=!0):this.dispatch(this.target.node,"cancelswipe"),this.setState(this.states.idle),!s}}},reorder:function(){function t(){this.updateScrolling(),s&&(clearTimeout(s),s=null);var t=this.getTotalMovement();this.target.node.style[c]="translate(0,"+t.y+"px) "+m+this.target.baseTransform.value;var e=this.target.height;return v.forEach(function(i){var n=0;i.pos<0&&t.y<0&&i.pos>t.y?n=e:i.pos>0&&t.y>0&&i.pos<t.y&&(n=-e),i.node.style[c]=n?"translate(0,"+n+"px) "+g+i.baseTransform.value:i.baseTransform.original}),!1}this.target.node.focus&&o.focus&&this.target.node.focus(),this.target.height=this.target.node.offsetHeight;for(var s,a=this.container.childNodes,h=i(this.target,a),f=this.target.node.offsetTop+this.target.height/2,v=[],p=0;p<a.length;p++)if(1==a[p].nodeType&&a[p]!==this.target.node){var T=a[p].offsetTop;a[p].style[l]=u+" 0.2s ease-in-out",v.push({node:a[p],baseTransform:e(a[p]),pos:T+(T<f?a[p].offsetHeight:0)-f})}return this.target.node.classList.add("slip-reordering"),this.target.node.style.zIndex="99999",this.target.node.style[d]="none",r&&(this.container.style.webkitTransformStyle="preserve-3d"),t.call(this),{leaveState:function(){s&&clearTimeout(s),r&&(this.container.style.webkitTransformStyle=""),this.container.focus&&n.focus&&this.container.focus(),this.target.node.classList.remove("slip-reordering"),this.target.node.style[d]="",this.animateToZero(function(t){t.node.style.zIndex=""}),v.forEach(function(t){t.node.style[c]=t.baseTransform.original,t.node.style[l]=""})},onMove:t,onLeave:function(){s&&clearTimeout(s),s=setTimeout(function(){s=null,this.cancel()}.bind(this),700)},onEnd:function(){var t,e,i=this.getTotalMovement();if(i.y<0){for(t=0;t<v.length&&!(v[t].pos>i.y);t++);e=t}else{for(t=v.length-1;t>=0&&!(v[t].pos<i.y);t--);e=t+1}return this.dispatch(this.target.node,"reorder",{spliceIndex:e,originalIndex:h,insertBefore:v[e]?v[e].node:null}),this.setState(this.states.idle),!1}}}},attach:function(t){f++,this.container&&this.detach(),!v&&a&&(v=!0,document.body.addEventListener("touchstart",p,!1)),this.container=t,!1!==n.tabIndex&&(this.container.tabIndex=n.tabIndex),n.ariaRole&&this.container.setAttribute("aria-role",n.ariaRole),this.setChildNodesAriaRoles(),this.container.addEventListener("focus",this.onContainerFocus,!1),this.otherNodes=[],document.addEventListener("selectionchange",this.onSelection,!1),this.container.addEventListener("touchcancel",this.cancel,!1),this.container.addEventListener("touchstart",this.onTouchStart,!1),this.container.addEventListener("touchmove",this.onTouchMove,!1),this.container.addEventListener("touchend",this.onTouchEnd,!1),this.container.addEventListener("mousedown",this.onMouseDown,!1)},detach:function(){this.cancel(),this.container.removeEventListener("mousedown",this.onMouseDown,!1),this.container.removeEventListener("touchend",this.onTouchEnd,!1),this.container.removeEventListener("touchmove",this.onTouchMove,!1),this.container.removeEventListener("touchstart",this.onTouchStart,!1),this.container.removeEventListener("touchcancel",this.cancel,!1),document.removeEventListener("selectionchange",this.onSelection,!1),!1!==n.tabIndex&&this.container.removeAttribute("tabIndex"),n.ariaRole&&this.container.removeAttribute("aria-role"),this.unSetChildNodesAriaRoles(),!--f&&v&&(v=!1,document.body.removeEventListener("touchstart",p,!1))},setState:function(t){if(this.state){if(this.state.ctor===t)return;this.state.leaveState&&this.state.leaveState.call(this)}var e=this.state,i=t.call(this);this.state===e&&(i.ctor=t,this.state=i)},findTargetNode:function(t){for(;t&&t.parentNode!==this.container;)t=t.parentNode;return t},onContainerFocus:function(t){this.setChildNodesAriaRoles()},setChildNodesAriaRoles:function(){for(var t=this.container.childNodes,e=0;e<t.length;e++)1==t[e].nodeType&&(o.ariaRole&&t[e].setAttribute("aria-role",o.ariaRole),!1!==o.tabIndex&&(t[e].tabIndex=o.tabIndex))},unSetChildNodesAriaRoles:function(){for(var t=this.container.childNodes,e=0;e<t.length;e++)1==t[e].nodeType&&(o.ariaRole&&t[e].removeAttribute("aria-role"),!1!==o.tabIndex&&t[e].removeAttribute("tabIndex"))},onSelection:function(t){var e=t.target===document||this.findTargetNode(t),i=/(iPhone|iPad|iPod)/i.test(navigator.userAgent)&&!/(Android|Windows)/i.test(navigator.userAgent);e&&(i?this.setState(this.states.idle):this.state.allowTextSelection||t.preventDefault())},addMouseHandlers:function(){this.mouseHandlersAttached||(this.mouseHandlersAttached=!0,document.documentElement.addEventListener("mouseleave",this.onMouseLeave,!1),window.addEventListener("mousemove",this.onMouseMove,!0),window.addEventListener("mouseup",this.onMouseUp,!0),window.addEventListener("blur",this.cancel,!1))},removeMouseHandlers:function(){this.mouseHandlersAttached&&(this.mouseHandlersAttached=!1,document.documentElement.removeEventListener("mouseleave",this.onMouseLeave,!1),window.removeEventListener("mousemove",this.onMouseMove,!0),window.removeEventListener("mouseup",this.onMouseUp,!0),window.removeEventListener("blur",this.cancel,!1))},onMouseLeave:function(t){this.usingTouch||t.target!==document.documentElement&&t.relatedTarget!==document.documentElement||this.state.onLeave&&this.state.onLeave.call(this)},onMouseDown:function(t){!this.usingTouch&&0==t.button&&this.setTarget(t)&&(this.addMouseHandlers(),this.canPreventScrolling=!0,this.startAtPosition({x:t.clientX,y:t.clientY,time:t.timeStamp}))},onTouchStart:function(t){this.usingTouch=!0,this.canPreventScrolling=!0,t.touches.length>1?this.setState(this.states.idle):this.setTarget(t)&&this.startAtPosition({x:t.touches[0].clientX,y:t.touches[0].clientY,time:t.timeStamp})},setTarget:function(t){var i=this.findTargetNode(t.target);if(!i)return this.setState(this.states.idle),!1;for(var n=i.parentNode;n&&n!=document.body&&!(n.scrollHeight>n.clientHeight&&"visible"!=window.getComputedStyle(n)["overflow-y"]);)n=n.parentNode;return n=n||document.body,this.target={originalTarget:t.target,node:i,scrollContainer:n,origScrollTop:n.scrollTop,origScrollHeight:n.scrollHeight,baseTransform:e(i)},!0},startAtPosition:function(t){this.startPosition=this.previousPosition=this.latestPosition=t,this.setState(this.states.undecided)},updatePosition:function(t,e){null!=this.target&&(this.latestPosition=e,this.state.onMove&&!1===this.state.onMove.call(this)&&t.preventDefault(),this.latestPosition.time-this.previousPosition.time>100&&(this.previousPosition=this.latestPosition))},onMouseMove:function(t){this.updatePosition(t,{x:t.clientX,y:t.clientY,time:t.timeStamp})},onTouchMove:function(t){this.updatePosition(t,{x:t.touches[0].clientX,y:t.touches[0].clientY,time:t.timeStamp}),this.canPreventScrolling=!1},onMouseUp:function(t){this.usingTouch||0!==t.button||this.state.onEnd&&!1===this.state.onEnd.call(this)&&t.preventDefault()},onTouchEnd:function(t){t.touches.length>1?this.cancel():this.state.onEnd&&!1===this.state.onEnd.call(this)&&t.preventDefault()},getTotalMovement:function(){var t=this.target.scrollContainer.scrollTop-this.target.origScrollTop;return{x:this.latestPosition.x-this.startPosition.x,y:this.latestPosition.y-this.startPosition.y+t,time:this.latestPosition.time-this.startPosition.time}},getAbsoluteMovement:function(){var t=this.getTotalMovement();return{x:Math.abs(t.x),y:Math.abs(t.y),time:t.time,directionX:t.x<0?"left":"right",directionY:t.y<0?"up":"down"}},updateScrolling:function(){var t=0,e=this.target.scrollContainer,i=e.getBoundingClientRect(),n=this.target.node.getBoundingClientRect(),o=Math.min(i.bottom,window.innerHeight)-n.bottom,s=n.top-Math.max(i.top,0),a=this.target.origScrollHeight-Math.min(e.clientHeight,window.innerHeight);o<40?t=Math.min(40,40-o):s<40&&(t=Math.max(-40,s-40)),e.scrollTop=Math.max(0,Math.min(a,e.scrollTop+t))},dispatch:function(t,e,i){var n=document.createEvent("CustomEvent");return n&&n.initCustomEvent?n.initCustomEvent("slip:"+e,!0,!0,i):((n=document.createEvent("Event")).initEvent("slip:"+e,!0,!0),n.detail=i),t.dispatchEvent(n)},getSiblings:function(t){for(var i=[],n=t.node.nextSibling;n;)1==n.nodeType&&i.push({node:n,baseTransform:e(n)}),n=n.nextSibling;return i},animateToZero:function(t,e){(e=e||this.target).node.style[l]=u+" 0.1s ease-out",e.node.style[c]="translate(0,0) "+g+e.baseTransform.value,setTimeout(function(){e.node.style[l]="",e.node.style[c]=e.baseTransform.original,t&&t.call(this,e)}.bind(this),101)},animateSwipe:function(t){var e=this.target,i=this.getSiblings(e),n="translate(0,"+this.target.height+"px) "+g+" ";e.node.style[l]="all 0.1s linear",e.node.style[c]=" translate("+(this.getTotalMovement().x>0?"":"-")+"100%,0) "+g+e.baseTransform.value,setTimeout(function(){t.call(this,e)&&(i.forEach(function(t){t.node.style[l]="",t.node.style[c]=n+t.baseTransform.value}),setTimeout(function(){i.forEach(function(t){t.node.style[l]=u+" 0.1s ease-in-out",t.node.style[c]="translate(0,0) "+g+t.baseTransform.value}),setTimeout(function(){i.forEach(function(t){t.node.style[l]="",t.node.style[c]=t.baseTransform.original})},101)},1))}.bind(this),101)}},"function"==typeof define&&define.amd&&define(function(){return t}),"object"==typeof module&&module.exports&&(module.exports=t),t}();var slipLoaded = true;
}

(() => {
    if( document.querySelector(".full-item") === null ){
        return;
    }
    let list = document.querySelector(".full-item").parentElement;
    list.addEventListener("slip:beforewait", e => {
        if (e.target.classList.contains("draggable")) e.preventDefault();
    }, false);

    list.addEventListener("slip:reorder", e => {
        e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
        $.post("/requests.php", $("#reorder-items-form").serialize(), r => {
           console.log(r);
        });
        return false;
    }, false);
    return new Slip(list);
})();

$(".change-candidate-pfp").on("click", () => {
    $("#pfp-upload input[type='file']").click();
});

$("#pfp-upload input[type='file']").on("change", ev => {
    // TODO MAYBE SOME ISSUE HERE / CHECK FOR FILE TYPES
    let element = ev.currentTarget;
    let file = element.files[0];
    let errors = [];

    if( ! file.type.startsWith("image/") ){
        errors.push("The file uploaded is not an image");
    }

    if( file.size > 2000000 ){
        errors.push("That file is too large. The file size limit is 2MB");
    }

    if( errors.length === 0 ){
        let reader = new FileReader();
        reader.onload = e => {
            showConfirmation(document.getElementById("pfp-confirm"), () => {
                $(".candidate-photo").attr("src", reader.result);
                // Send out the updated image to the server
                $.ajax({
                    type: 'POST',
                    url:"/requests.php",
                    data: new FormData($("#pfp-upload")[0]),
                    processData: false,
                    contentType: false,
                    success: r => {
                        console.log(r);
                    }
                });
            });
            $(".pfp-confirm-set-src").attr("src", reader.result);
        };
        reader.readAsDataURL(file);
    } else {
        for( let x = 0 ; x < errors.length ; x++ ){
            addSnackbarQueue(errors[x]);
        }
        playSnackbarQueue();
    }
});

$(".new-info").on("click", ev => {
    showConfirmation(
        document.querySelector("#new-basic-info"),
        () => {
            $("#basic-info-token").val($(ev.currentTarget).data("form-token"));
            $.post("/requests.php", $("#basic-info-form").serialize() ,r => {
                let data = JSON.parse(r);
                if( data.status === "success" ){
                    changePage();
                } else {
                    immediateSnackbarList(data.message);
                }
            });
        }, () => {},
        "Add Basic Info"
    );
});

$(".delete-info").on("click", ev => {
    let type = ev.currentTarget.parentElement.parentElement.querySelector(".info-type").innerHTML;
    let content = ev.currentTarget.parentElement.parentElement.querySelector(".info-content").innerHTML;
    showConfirmation(document.querySelector("#delete-basic-confirm"), () => {
        // Send the post request to delete it.
        $.post("/requests.php", {"token": $(ev.currentTarget).data("form-token") }, r => {
            let data = JSON.parse(r);
            if( data.status === "success" ){
                changePage();
            } else {
                immediateSnackbarList(data.message)
            }
        });
    }, () => {
        // Do nothing
    }, "Confirm Deletion");
    $("#info-type-display").html(type);
    $("#info-content-display").html(content);
});

$(".edit-info").on("click", ev => {
    let type = ev.currentTarget.parentElement.parentElement.querySelector(".info-type").getAttribute("data-actual-type");
    let content = ev.currentTarget.parentElement.parentElement.querySelector(".info-content").getAttribute("data-actual-content");
    showConfirmation(document.querySelector("#new-basic-info"), () => {
        // Send the post request to delete it.
        $.post("/requests.php", $("#basic-info-form").serialize() , r => {
            let data = JSON.parse(r);
            if( data.status === "success" ){
                changePage();
            } else {
                immediateSnackbarList(data.message)
            }
        });
    }, () => {
        // Do nothing
    }, "Edit Basic Info");
    $("#new-info-type-input").val(type);
    $("#new-info-content-input").val(content);
    $("#basic-info-token").val($(ev.currentTarget).data("form-token"));
});