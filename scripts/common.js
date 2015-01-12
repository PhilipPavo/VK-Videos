var app = angular.module('vk-videos', ['ui.bootstrap']);
app.run(function($rootScope, AJAX, VK) {
	
});
app.controller('main', function($scope){
	
});
app.service('HTTP', function ($http) {
    this.data2str = function(d){
			var $str = "";
			angular.forEach(d, function(v, k) {
			    $str += k+"="+v+"&";
			});
      //console.log($str);
			return $str;
		};
    this.post = function(u, d, c, f){
        $http({
  			    url: u,
  			    method: "POST",
  			    data: this.data2str(d),
  				headers : {'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'
                      //,'User agent' : 'Dalvik/1.6.0 (Linux; U; Android 4.1.2; Galaxy S3 Build/JZO54K)'
                      }
  			}).success(c).error(f ? f : function(d){
          //console.log(d); 
        });
    };
    this.get = function (url, js_cb) {
        // $http() returns a $promise that we can add handlers with .then()
       // console.log("request to: " + url);
        return $http.jsonp(url+(js_cb ? "&callback=JSON_CALLBACK": ""));
    };
  });
app.service('VK', function (HTTP, $rootScope) {
    var VK= this;
	this.api = function (m, d, c, f) {
            var url = "https://api.vk.com/method/" + m;
            url += "?v=" + VK.version + "&https=1&access_token=" + USER.access_token;
            angular.forEach(d, function (value, key) {
                url += ("&" + key + "=" + value);
            });
            HTTP.get(url, true).then(function (r) {
                r = r.data;
                if (r.response) c(r);
                else f ? f(r.error, url) : console.error("ERROR: in '"+url+"'; MESSAGE: "+angular.toJson(r.error));
            });
        },
    this.version = "5.21";
  });
app.service('AJAX', function ($http, $rootScope) {
	this.post = function(m, d, c, f){
			d.method = m;
			$http({
  			    url: 'ajax/call.php',
  			    method: "POST",
  			    data: d
  			}).success(function(d){d.error ? c(false) : (c ? c(d.response) : console.log(d))}).error(f ? f : function(d){console.log(d);});
	}
});