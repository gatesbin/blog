var gulp=require("gulp"),gulpPrint=require("gulp-print"),gulpUglify=require("gulp-uglify"),gulpCleanCss=require("gulp-clean-css"),gulpLess=require("gulp-less");gulp.task("buildJs",function(){return gulp.src("src/**/*.js").pipe(gulpPrint(function(u){return"build: "+u})).pipe(gulpUglify()).pipe(gulp.dest("dist"))}),gulp.task("buildLess",function(){return gulp.src("src/**/*.less").pipe(gulpPrint(function(u){return"build: "+u})).pipe(gulpLess()).pipe(gulpCleanCss({keepSpecialComments:0})).pipe(gulp.dest("dist"))}),gulp.task("default",["buildJs","buildLess"],function(){});