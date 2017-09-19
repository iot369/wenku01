
// Provide a default path to dwr.engine
if (dwr == null) var dwr = {};
if (dwr.engine == null) dwr.engine = {};
if (DWREngine == null) var DWREngine = dwr.engine;

if (docinNew == null) var docinNew = {};
docinNew._path = '/dwr';
docinNew.deleteById = function(p0, p1, p2, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'deleteById', p0, p1, p2, callback);
}
docinNew.updateBlogName = function(p0, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'updateBlogName', p0, callback);
}
docinNew.findBookNumAndVisitCount = function(p0, p1, p2, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'findBookNumAndVisitCount', p0, p1, p2, callback);
}
docinNew.checkOldPwd = function(p0, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'checkOldPwd', p0, callback);
}
docinNew.addFloader = function(p0, p1, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'addFloader', p0, p1, callback);
}
docinNew.addFavoriteFolder = function(p0, p1, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'addFavoriteFolder', p0, p1, callback);
}
docinNew.selectFloder = function(p0, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'selectFloder', p0, callback);
}
docinNew.selectFavoriteFloder = function(p0, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'selectFavoriteFloder', p0, callback);
}
docinNew.updateFolder = function(p0, p1, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'updateFolder', p0, p1, callback);
}
docinNew.updateFavoriteFolder = function(p0, p1, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'updateFavoriteFolder', p0, p1, callback);
}
docinNew.deleteFolder = function(p0, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'deleteFolder', p0, callback);
}
docinNew.deleteFavoriteFolder = function(p0, callback) {
  dwr.engine._execute(docinNew._path, 'docinNew', 'deleteFavoriteFolder', p0, callback);
}
