
// Provide a default path to dwr.engine
if (dwr == null) var dwr = {};
if (dwr.engine == null) dwr.engine = {};
if (DWREngine == null) var DWREngine = dwr.engine;

if (checkLoginDwr == null) var checkLoginDwr = {};
checkLoginDwr._path = '/dwr';
checkLoginDwr.checkLoginName = function(p0, callback) {
  dwr.engine._execute(checkLoginDwr._path, 'checkLoginDwr', 'checkLoginName', p0, callback);
}
checkLoginDwr.checkLoginEmail = function(p0, callback) {
  dwr.engine._execute(checkLoginDwr._path, 'checkLoginDwr', 'checkLoginEmail', p0, callback);
}
checkLoginDwr.checkCode = function(p0, callback) {
  dwr.engine._execute(checkLoginDwr._path, 'checkLoginDwr', 'checkCode', p0, callback);
}
checkLoginDwr.friendEmail = function(p0, p1, p2, p3, p4, p5, p6, callback) {
  dwr.engine._execute(checkLoginDwr._path, 'checkLoginDwr', 'friendEmail', p0, p1, p2, p3, p4, p5, p6, false, callback);
}
checkLoginDwr.sendMailAgain = function(callback) {
  dwr.engine._execute(checkLoginDwr._path, 'checkLoginDwr', 'sendMailAgain', callback);
}
checkLoginDwr.sendValidateMailAgain = function(callback) {
  dwr.engine._execute(checkLoginDwr._path, 'checkLoginDwr', 'sendValidateMailAgain', callback);
}
checkLoginDwr.checkCanValidateEmail = function(callback) {
  dwr.engine._execute(checkLoginDwr._path, 'checkLoginDwr', 'checkCanValidateEmail', callback);
}
checkLoginDwr.sendMailLoginEmail2 = function(callback) {
  dwr.engine._execute(checkLoginDwr._path, 'checkLoginDwr', 'sendMailLoginEmail2', callback);
}
checkLoginDwr.checkPerson = function(p0, callback) {
  dwr.engine._execute(checkLoginDwr._path, 'checkLoginDwr', 'checkPerson', p0, callback);
}
