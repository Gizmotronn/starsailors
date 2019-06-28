function Initialize()
	screens = {
		{"Welcome","Welcome.png","600"},
		{"Desktop","Desktop.png","830"},
		{"Dock","Dock.png","1300"},
		{"Top Bar","TopBar.png","1700"},
		{"Drawer","Drawer.png","1060"},
		{"Game Drawer","GameDrawer.png","1100"},
		{"Command","Command.png","700"},
		{"Clipboard History","Clipboard.png","500"},
		{"Visualizer Settings","Visualizer.png","800"}
	}
	CurrPage=nil
	AmountToScroll=tonumber(SKIN:GetVariable('Scroll.Distance'))
	DisplayAreaHeight=tonumber(SKIN:GetVariable('ViewAreaHeight'))
	ScrollPos=0

	Draw()
	Select(1)
end

function Draw()
	for k,v in ipairs(screens) do
		SKIN:Bang('!SetOption','Item'..k,'Text',v[1])
		SKIN:Bang('!SetOption','Item'..k,'LeftMouseUpAction','!CommandMeasure Script Select('..k..')')
	end
	local num = table.getn(screens)
	SKIN:Bang("!SetOption","SepSection","H",(25*num))
end

function Select(num)
	CurrPage=num
	ScrollPos=0
	ScrollTotal=math.ceil((tonumber(screens[num][3])- DisplayAreaHeight)/AmountToScroll)
	SKIN:Bang('!SetVariable','Scroll.Position',ScrollPos)
	SKIN:Bang('!SetVariable','Scroll.Total',ScrollTotal)
	if ScrollTotal > 1 then
		SKIN:Bang('!ShowMeterGroup Scroll')
	else
		SKIN:Bang('!HideMeterGroup Scroll')
	end
	SKIN:Bang('!SetOptionGroup','List','MeterStyle','ItemStyle')
	SKIN:Bang('!SetOption','Item'..num,'MeterStyle','ItemStyle|ItemStyleActive')
	SKIN:Bang('!SetOption','Image','ImageName','Resources\\Images\\'..screens[num][2])
	SKIN:Bang('!Update')
end

function GoTo(name)
	for k,v in ipairs(screens) do
		if v[1] == name then
			Select(k)
			return
		end
	end
end


function ScrollDown()
	if ScrollPos < math.ceil(((tonumber(screens[CurrPage][3])- DisplayAreaHeight)/AmountToScroll)) then
		ScrollPos = ScrollPos + 1
		SKIN:Bang('!SetVariable','Scroll.Position',ScrollPos)
		SKIN:Bang('!Update')
	end
end

function ScrollUp()
	if ScrollPos >= 1 then
		ScrollPos = (ScrollPos - 1)
		SKIN:Bang('!SetVariable','Scroll.Position',ScrollPos)
		SKIN:Bang('!Update')
	end
end

function ScrollTo(num)
	ScrollPos = num
	SKIN:Bang('!SetVariable','Scroll.Position',ScrollPos)
	SKIN:Bang('!Update')
end