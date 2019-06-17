require "/scripts/util.lua"
require "/scripts/vec2.lua"
require "/quests/scripts/portraits.lua"
require "/quests/scripts/questutil.lua"

function init()
  setPortraits()

  storage.complete = storage.complete or false

  self.compassUpdate = config.getParameter("compassUpdate", 0.5)

  storage.stage =  1
  self.stages = {
    turnIn   
  }

  self.state = FSM:new()
  self.state:set(self.stages[storage.stage])
  
   self.warpEntity = config.getParameter("warpEntityUid")
  self.warpAction = config.getParameter("warpAction")
  self.warpDialog = config.getParameter("warpDialog")
end

function questStart()
  local associatedMission = config.getParameter("associatedMission")
  if associatedMission then
    player.enableMission(associatedMission)
  end
end

function enterInstance(dt)
  quest.setCompassDirection(nil)
  quest.setObjectiveList({
    {self.descriptions.enterInstance, false}
  })
  quest.setParameter("warpentity", {type = "entity", uniqueId = self.warpEntity})
  quest.setIndicators({"warpentity"})

  self.onInteract = function(entityId)
    if world.entityUniqueId(entityId) == self.warpEntity then
      if not self.warpConfirmation then
        local dialogConfig = root.assetJson(self.warpDialog)
        dialogConfig.sourceEntityId = entityId
        self.warpConfirmation = player.confirm(dialogConfig)
      end
      return true
    end
  end

  
  
  local findWarpEntity = util.uniqueEntityTracker(self.warpEntity, 0.5)
  
  while storage.stage == 1 do
    questutil.pointCompassAt(findWarpEntity())

    

    if self.warpConfirmation and self.warpConfirmation:finished() then
      if self.warpConfirmation:result() then
        if type(self.warpAction) == "string" then
          player.warp(self.warpAction, "beam")
        elseif type(self.warpAction) == "table" then
          player.warp(self.warpAction[1], self.warpAction[2], self.warpAction[3])
        end
      end
      self.warpConfirmation = nil
    end

    coroutine.yield()
  end

  self.state:set(self.stages[storage.stage])
end

function questInteract(entityId)
  if self.onInteract then
    return self.onInteract(entityId)
  end
end


function questComplete()
  setPortraits()
  questutil.questCompleteActions()
end


function turnIn()
  quest.setIndicators({})
  quest.setCompassDirection(nil)
  quest.setCanTurnIn(true)

  quest.setObjectiveList({{config.getParameter("descriptions.turnIn"), false}})

  local findOak = util.uniqueEntityTracker(self.oakUid, self.compassUpdate)
  while true == 1 do
    questutil.pointCompassAt(findOak())

    coroutine.yield()
  end
end