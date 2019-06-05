<form name="Keypad" action="" method="" onSubmit="return false" style="display:inline">
  <table width="282" cellspacing="0" border="0" style="background-color:#E0DFE3;padding:2px">
    <tbody>
      <tr>
        <td colspan="3" align='center' width='60%'><input size="20" value="0" name="ReadOut" READONLY onFocus="return false;"></td>
        <td align="center" width='20%'><input style="width:35px;height:20px;color:#ff0000" onclick="Clear()" type="button" value="C" name="btnClear"></td>
        <td align="center" width='20%'><input style="width:35px;height:20px;color:#ff0000" onclick="clearEntry()" type="button" value="CE" name="btnClearEntry"></td>
      </tr>
      <tr>
        <td valign="top" align="center"><input style="width:35px;height:20px;color:#0000ff" onclick="numPressed(7)" type="button" value="7" name="btnSeven"></td>
        <td valign="top" align="center"><input style="width:35px;height:20px;color:#0000ff" onclick="numPressed(8)" type="button" value="8" name="btnEight"></td>
        <td valign="top" align="center"><input style="width:35px;height:20px;color:#0000ff" onclick="numPressed(9)" type="button" value="9" name="btnNine"></td>
        <td valign="top" align="center"><input style="width:35px;height:20px;color:#ff0000" onclick="Neg()" type="button" value="+/-" name="btnNeg"></td>
        <td valign="top" align="center"><input style="width:35px;height:20px;color:#ff0000" onclick="Percent()" type="button" value="%" name="btnPercent"></td>
      </tr>
      <tr>
        <td align="center" valign="top"><input style="width:35px;height:20px;color:#0000ff" onclick="numPressed(4)" type="button" value="4" name="btnFour"></td>
        <td align="center" valign="top"><input style="width:35px;height:20px;color:#0000ff" onclick="numPressed(5)" type="button" value="5" name="btnFive"></td>
        <td align="center" valign="top"><input style="width:35px;height:20px;color:#0000ff" onclick="numPressed(6)" type="button" value="6" name="btnSix"></td>
        <td align="center"><input style="width:35px;height:20px;color:#ff0000" onclick="Operation('+')" type="button" value="+" name="btnPlus"></td>
        <td align="center"><input style="width:35px;height:20px;color:#ff0000" onclick="Operation('-')" type="button" value="-" name="btnMinus"></td>
      </tr>
      <tr>
        <td align="center" valign="top"><input style="width:35px;height:20px;color:#0000ff" onclick="numPressed(1)" type="button" value="1" name="btnOne"></td>
        <td align="center" valign="top"><input style="width:35px;height:20px;color:#0000ff" onclick="numPressed(2)" type="button" value="2" name="btnTwo"></td>
        <td align="center" valign="top"><input style="width:35px;height:20px;color:#0000ff" onclick="numPressed(3)" type="button" value="3" name="btnThree"></td>
        <td align="center"><input style="width:35px;height:20px;color:#ff0000" onclick="Operation('*')" type="button" value="*" name="btnMultiply"></td>
        <td align="center"><input style="width:35px;height:20px;color:#ff0000" onclick="Operation('/')" type="button" value="/" name="btnDivide"></td>
      </tr>
      <tr>
        <td align="center"><input style="width:35px;height:20px;color:#0000ff" onclick="numPressed(0)" type="button" value="0" name="btnZero"></td>
        <td align="center"><input style="width:35px;height:20px;color:#0000ff" onclick="Decimal()" type="button" value="." name="btnDecimal"></td>
        <td colspan='2' align="center"><input style="color:#0000ff" onclick="GetResult()" type="button" value="{result_caption}" name="btnResult"></td>
        <td align="center"><input style="width:35px;height:20px;color:#ff0000" onclick="Operation('=')" type="button" value="=" name="btnEquals"></td>
      </tr>
    </tbody>
  </table>
</form>