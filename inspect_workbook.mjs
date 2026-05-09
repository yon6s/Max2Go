import { FileBlob, SpreadsheetFile } from '@oai/artifact-tool';
const input = await FileBlob.load('/Users/wang/Downloads/Max2Go.xlsx');
const workbook = await SpreadsheetFile.importXlsx(input);
console.log(await workbook.help('worksheets'));
