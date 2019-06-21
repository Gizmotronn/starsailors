wh321util = {}

function wh321util.closestMonNPCTarget(range)	--the same as util.closestValidTarget(range) only considering monsters and npcs
  local newTargets = world.entityQuery(entity.position(), range, { includedTypes = { "npc", "monster"}, order = "nearest" })
  local valid = util.find(newTargets, function(targetId) return entity.isValidTarget(targetId) and entity.entityInSight(targetId) end)
  return valid or 0
end

function wh321util.mostFarMonNPCTarget(range)  --queries the most far npc or monster in target range
  local newTargets = world.entityQuery(entity.position(), range, { includedTypes = { "npc", "monster"}, order = "nearest" })
  local valid = util.find(newTargets, function(targetId) return entity.isValidTarget(targetId) and entity.entityInSight(targetId) end, #newTargets)
  return valid or 0
end

function wh321util.targetAtPos(aimingPos, range)	--queries target at the given position
  local newTargets = world.entityQuery(aimingPos, range, { includedTypes = { "npc", "monster"}, order = "nearest" })
  local valid = util.find(newTargets, function() return true end)
  return valid or 0
end